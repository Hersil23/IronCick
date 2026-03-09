<?php

require_once __DIR__ . '/../models/Proveedor.php';

class ProveedoresController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function index(int $panelId): void {
        Auth::check('panel');
        $model = new Proveedor($this->db);
        Response::success($model->all($panelId));
    }

    public function create(int $panelId): void {
        Auth::check('panel');
        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['nombre'])) {
            Response::error('Nombre es requerido.');
        }

        $model = new Proveedor($this->db);
        $input['panel_id'] = $panelId;
        $id = $model->create($input);

        Response::success(['id' => $id], 'Proveedor creado.', 201);
    }

    public function update(int $id, int $panelId): void {
        Auth::check('panel');
        $input = json_decode(file_get_contents('php://input'), true);
        $model = new Proveedor($this->db);

        $proveedor = $model->findById($id, $panelId);
        if (!$proveedor) Response::error('Proveedor no encontrado.', 404);

        $data = [];
        $allowed = ['nombre', 'contacto', 'telefono', 'notas', 'estado'];
        foreach ($allowed as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

        $model->update($id, $data);
        Response::success(null, 'Proveedor actualizado.');
    }
}
