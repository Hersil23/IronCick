<?php

require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Venta.php';

class ClientesController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function index(int $panelId): void {
        $model = new Cliente($this->db);
        $search = $_GET['q'] ?? '';
        $estado = $_GET['estado'] ?? '';
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

        $vendedorId = 0;
        if (Auth::isVendedor()) {
            $vendedorId = Auth::usuarioId();
        } elseif (Auth::isDistribuidor()) {
            // Distribuidor ve sus clientes y los de sus vendedores
            // handled by query
        }

        $total = $model->count($panelId, $search, $estado, $vendedorId);
        $pag = paginacion($total, $pagina);
        $clientes = $model->all($panelId, $search, $estado, $vendedorId, $pag['por_pagina'], $pag['offset']);

        Response::success([
            'clientes'   => $clientes,
            'paginacion' => $pag,
        ]);
    }

    public function create(int $panelId): void {
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['nombre']) || empty($input['telefono'])) {
            Response::error('Nombre y telefono son requeridos.');
        }

        $model = new Cliente($this->db);
        $input['panel_id'] = $panelId;

        if (Auth::isVendedor() || Auth::isDistribuidor()) {
            $input['vendedor_id'] = Auth::usuarioId();
        }

        $id = $model->create($input);
        Response::success(['id' => $id], 'Cliente creado.', 201);
    }

    public function update(int $id, int $panelId): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $model = new Cliente($this->db);

        $cliente = $model->findById($id, $panelId);
        if (!$cliente) Response::error('Cliente no encontrado.', 404);

        $data = [];
        $allowed = ['nombre', 'apellido', 'email', 'telefono'];
        foreach ($allowed as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

        $model->update($id, $data);
        Response::success(null, 'Cliente actualizado.');
    }

    public function updateEstado(int $id, int $panelId): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $estado = $input['estado'] ?? '';

        if (!in_array($estado, ['activo', 'suspendido', 'eliminado'])) {
            Response::error('Estado invalido.');
        }

        $model = new Cliente($this->db);
        if ($estado === 'eliminado') {
            $model->softDelete($id);
        } else {
            $model->updateEstado($id, $estado);
        }

        Response::success(null, 'Estado actualizado.');
    }

    public function ventas(int $id, int $panelId): void {
        $ventaModel = new Venta($this->db);
        Response::success($ventaModel->byCliente($id, $panelId));
    }
}
