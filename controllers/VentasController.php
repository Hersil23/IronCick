<?php

require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Perfil.php';
require_once __DIR__ . '/../models/Servicio.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Pago.php';
require_once __DIR__ . '/../models/Credito.php';

class VentasController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function index(int $panelId): void {
        $model = new Venta($this->db);
        $filtros = [
            'tab'         => $_GET['tab'] ?? '',
            'servicio_id' => (int) ($_GET['servicio'] ?? 0),
            'vendedor_id' => (int) ($_GET['vendedor'] ?? 0),
            'fecha'       => $_GET['fecha'] ?? '',
            'q'           => $_GET['q'] ?? '',
        ];

        if (Auth::isVendedor()) {
            $filtros['vendedor_id'] = Auth::usuarioId();
        }

        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $total = $model->count($panelId, $filtros);
        $pag = paginacion($total, $pagina);
        $ventas = $model->all($panelId, $filtros, $pag['por_pagina'], $pag['offset']);
        $tabs = $model->tabs($panelId);

        Response::success([
            'ventas'     => $ventas,
            'tabs'       => $tabs,
            'paginacion' => $pag,
        ]);
    }

    public function create(int $panelId): void {
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['cliente_id']) || empty($input['servicio_id'])) {
            Response::error('Cliente y servicio son requeridos.');
        }

        $this->db->beginTransaction();

        try {
            $servicioModel = new Servicio($this->db);
            $servicio = $servicioModel->findById((int)$input['servicio_id'], $panelId);
            if (!$servicio) throw new Exception('Servicio no encontrado.');

            $perfilModel = new Perfil($this->db);
            $perfil = $perfilModel->disponible((int)$input['servicio_id'], $panelId);
            if (!$perfil) throw new Exception('No hay perfiles disponibles para este servicio.');

            if (!$perfilModel->marcarVendido($perfil['id'])) {
                throw new Exception('Perfil ya fue asignado. Intenta de nuevo.');
            }

            $ventaModel = new Venta($this->db);
            $ventaId = $ventaModel->crear([
                'panel_id'      => $panelId,
                'cliente_id'    => (int) $input['cliente_id'],
                'vendedor_id'   => Auth::isVendedor() || Auth::isDistribuidor() ? Auth::usuarioId() : ($input['vendedor_id'] ?? null),
                'servicio_id'   => (int) $input['servicio_id'],
                'cuenta_id'     => $perfil['cuenta_id'],
                'perfil_id'     => $perfil['id'],
                'numero_orden'  => generarOrden(),
                'precio_usd'    => (float) $servicio['precio_usd'],
                'precio_local'  => (float) ($input['precio_local'] ?? 0),
                'duracion_dias' => (int) $servicio['duracion_dias'],
                'tipo'          => 'nueva',
            ]);

            // Registrar pago
            $pagoModel = new Pago($this->db);
            $pagoModel->create([
                'panel_id'    => $panelId,
                'cliente_id'  => (int) $input['cliente_id'],
                'venta_id'    => $ventaId,
                'monto_usd'   => (float) $servicio['precio_usd'],
                'monto_local' => (float) ($input['precio_local'] ?? 0),
                'tasa_cambio' => (float) ($input['tasa_cambio'] ?? 1),
                'metodo'      => $input['metodo'] ?? '',
            ]);

            // Actualizar ultimo pago del cliente
            $clienteModel = new Cliente($this->db);
            $clienteModel->update((int)$input['cliente_id'], ['ultimo_pago' => date('Y-m-d H:i:s'), 'estado' => 'activo']);

            $this->db->commit();

            $venta = $ventaModel->findById($ventaId, $panelId);
            Response::success($venta, 'Venta creada.', 201);

        } catch (Exception $e) {
            $this->db->rollBack();
            Response::error($e->getMessage());
        }
    }

    public function renovar(int $id, int $panelId): void {
        $ventaModel = new Venta($this->db);
        $venta = $ventaModel->findById($id, $panelId);
        if (!$venta) Response::error('Venta no encontrada.', 404);

        $servicioModel = new Servicio($this->db);
        $servicio = $servicioModel->findById($venta['servicio_id'], $panelId);
        if (!$servicio) Response::error('Servicio no encontrado.', 404);

        $ventaModel->renovar($id, $panelId, (int) $servicio['duracion_dias']);

        $pagoModel = new Pago($this->db);
        $pagoModel->create([
            'panel_id'    => $panelId,
            'cliente_id'  => $venta['cliente_id'],
            'venta_id'    => $id,
            'monto_usd'   => (float) $servicio['precio_usd'],
        ]);

        $clienteModel = new Cliente($this->db);
        $clienteModel->update($venta['cliente_id'], ['ultimo_pago' => date('Y-m-d H:i:s'), 'estado' => 'activo']);

        Response::success(null, 'Venta renovada.');
    }

    public function marcarPago(int $id, int $panelId): void {
        $pagoModel = new Pago($this->db);
        $pagoModel->confirmar($id, Auth::usuarioId());
        Response::success(null, 'Pago confirmado.');
    }

    public function cobroGrupal(int $panelId): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $clienteId = (int) ($input['cliente_id'] ?? 0);

        if (!$clienteId) Response::error('Cliente requerido.');

        $ventaModel = new Venta($this->db);
        $ventas = $ventaModel->byCliente($clienteId, $panelId);

        $total = 0;
        $servicios = [];
        foreach ($ventas as $v) {
            $total += (float) $v['precio_usd'];
            $servicios[] = [
                'servicio' => $v['servicio_nombre'],
                'precio'   => (float) $v['precio_usd'],
                'estado'   => $v['estado'],
                'vence'    => $v['fecha_vencimiento'],
            ];
        }

        Response::success([
            'servicios' => $servicios,
            'total_usd' => $total,
        ]);
    }

    public function renovacionGrupal(int $panelId): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $ventaIds = $input['venta_ids'] ?? [];

        if (empty($ventaIds)) Response::error('Selecciona al menos una venta.');

        $this->db->beginTransaction();
        try {
            $ventaModel = new Venta($this->db);
            $servicioModel = new Servicio($this->db);
            $pagoModel = new Pago($this->db);

            foreach ($ventaIds as $ventaId) {
                $venta = $ventaModel->findById((int) $ventaId, $panelId);
                if (!$venta) continue;

                $servicio = $servicioModel->findById($venta['servicio_id'], $panelId);
                if (!$servicio) continue;

                $ventaModel->renovar((int) $ventaId, $panelId, (int) $servicio['duracion_dias']);

                $pagoModel->create([
                    'panel_id'   => $panelId,
                    'cliente_id' => $venta['cliente_id'],
                    'venta_id'   => (int) $ventaId,
                    'monto_usd'  => (float) $servicio['precio_usd'],
                ]);
            }

            $this->db->commit();
            Response::success(null, 'Renovacion grupal completada.');

        } catch (Exception $e) {
            $this->db->rollBack();
            Response::error($e->getMessage());
        }
    }

    public function delete(int $id, int $panelId): void {
        $ventaModel = new Venta($this->db);
        $ventaModel->delete($id, $panelId);
        Response::success(null, 'Venta eliminada.');
    }
}
