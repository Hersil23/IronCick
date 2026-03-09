<?php

require_once __DIR__ . '/../models/Cuenta.php';
require_once __DIR__ . '/../models/Perfil.php';

class CuentasController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function index(int $panelId): void {
        Auth::check('panel');
        $model = new Cuenta($this->db);

        $servicioId = (int) ($_GET['servicio'] ?? 0);
        $estado = $_GET['estado'] ?? '';
        $search = $_GET['q'] ?? '';
        $pagina = max(1, (int) ($_GET['pagina'] ?? 1));

        $total = $model->count($panelId, $servicioId, $estado, $search);
        $pag = paginacion($total, $pagina);
        $cuentas = $model->all($panelId, $servicioId, $estado, $search, $pag['por_pagina'], $pag['offset']);

        Response::success([
            'cuentas'    => $cuentas,
            'paginacion' => $pag,
        ]);
    }

    public function create(int $panelId): void {
        Auth::check('panel');
        $input = json_decode(file_get_contents('php://input'), true);

        $required = ['servicio_id', 'correo', 'password', 'costo_usd', 'fecha_vencimiento'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                Response::error("El campo $field es requerido.");
            }
        }

        $model = new Cuenta($this->db);
        $input['panel_id'] = $panelId;
        $id = $model->create($input);

        Response::success(['id' => $id], 'Cuenta creada con perfiles.', 201);
    }

    public function update(int $id, int $panelId): void {
        Auth::check('panel');
        $input = json_decode(file_get_contents('php://input'), true);
        $model = new Cuenta($this->db);

        $cuenta = $model->findById($id, $panelId);
        if (!$cuenta) Response::error('Cuenta no encontrada.', 404);

        $data = [];
        $allowed = ['correo', 'password', 'costo_usd', 'fecha_vencimiento', 'proveedor_id', 'estado'];
        foreach ($allowed as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

        $model->update($id, $data);
        Response::success(null, 'Cuenta actualizada.');
    }

    public function delete(int $id, int $panelId): void {
        Auth::check('panel');
        $model = new Cuenta($this->db);
        $model->delete($id, $panelId);
        Response::success(null, 'Cuenta eliminada.');
    }

    public function perfiles(int $cuentaId, int $panelId): void {
        Auth::check('panel');
        $cuentaModel = new Cuenta($this->db);
        $cuenta = $cuentaModel->findById($cuentaId, $panelId);
        if (!$cuenta) Response::error('Cuenta no encontrada.', 404);

        $perfilModel = new Perfil($this->db);
        Response::success($perfilModel->byCuenta($cuentaId));
    }
}
