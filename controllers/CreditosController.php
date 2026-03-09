<?php

require_once __DIR__ . '/../models/Credito.php';
require_once __DIR__ . '/../models/Panel.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Cliente.php';

class CreditosController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function movimientos(int $panelId): void {
        $model = new Credito($this->db);
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));
        $total = $model->countMovimientos($panelId);
        $pag = paginacion($total, $pagina);

        Response::success([
            'movimientos' => $model->movimientos($panelId, $pag['por_pagina'], $pag['offset']),
            'paginacion'  => $pag,
        ]);
    }

    public function venderAPanel(): void {
        Auth::check('super_admin');
        $input = json_decode(file_get_contents('php://input'), true);

        $panelId = (int) ($input['panel_id'] ?? 0);
        $monto = (float) ($input['monto'] ?? 0);

        if (!$panelId || $monto <= 0) {
            Response::error('Panel y monto valido requeridos.');
        }

        $panelModel = new Panel($this->db);
        $panel = $panelModel->findById($panelId);
        if (!$panel) Response::error('Panel no encontrado.', 404);

        $panelModel->updateCreditos($panelId, $monto);

        $creditoModel = new Credito($this->db);
        $creditoModel->registrar([
            'panel_id'     => $panelId,
            'tipo'         => 'entrada',
            'origen'       => 'super_admin',
            'destino_tipo' => 'panel',
            'destino_id'   => $panelId,
            'monto'        => $monto,
            'concepto'     => 'Venta de creditos por Super Admin',
        ]);

        Response::success(null, "Creditos acreditados al panel.");
    }

    public function generar(int $panelId): void {
        Auth::check('panel');
        $input = json_decode(file_get_contents('php://input'), true);

        $destinoId = (int) ($input['destino_id'] ?? 0);
        $monto = (float) ($input['monto'] ?? 0);

        if (!$destinoId || $monto <= 0) {
            Response::error('Destino y monto valido requeridos.');
        }

        $usuarioModel = new Usuario($this->db);
        $usuario = $usuarioModel->findById($destinoId, $panelId);
        if (!$usuario) Response::error('Usuario no encontrado.', 404);

        $usuarioModel->updateCreditos($destinoId, $monto);

        $creditoModel = new Credito($this->db);
        $creditoModel->registrar([
            'panel_id'     => $panelId,
            'tipo'         => 'entrada',
            'origen'       => 'panel',
            'destino_tipo' => $usuario['rol'],
            'destino_id'   => $destinoId,
            'monto'        => $monto,
            'concepto'     => 'Creditos generados por panel',
        ]);

        Response::success(null, "Creditos generados.");
    }

    public function acreditar(int $panelId): void {
        $input = json_decode(file_get_contents('php://input'), true);

        $clienteId = (int) ($input['cliente_id'] ?? 0);
        $monto = (float) ($input['monto'] ?? 0);

        if (!$clienteId || $monto <= 0) {
            Response::error('Cliente y monto valido requeridos.');
        }

        $clienteModel = new Cliente($this->db);
        $cliente = $clienteModel->findById($clienteId, $panelId);
        if (!$cliente) Response::error('Cliente no encontrado.', 404);

        $clienteModel->updateCreditos($clienteId, $monto);

        $creditoModel = new Credito($this->db);
        $creditoModel->registrar([
            'panel_id'     => $panelId,
            'tipo'         => 'entrada',
            'origen'       => Auth::rol(),
            'destino_tipo' => 'cliente',
            'destino_id'   => $clienteId,
            'monto'        => $monto,
            'concepto'     => 'Acreditacion a billetera de cliente',
        ]);

        Response::success(null, "Creditos acreditados al cliente.");
    }
}
