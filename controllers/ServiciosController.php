<?php

require_once __DIR__ . '/../models/Servicio.php';

class ServiciosController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function index(int $panelId): void {
        Auth::check('panel');
        $model = new Servicio($this->db);
        Response::success($model->all($panelId));
    }

    public function create(int $panelId): void {
        Auth::check('panel');
        $input = json_decode(file_get_contents('php://input'), true);

        $required = ['nombre', 'precio_usd', 'duracion_dias', 'perfiles_por_cuenta'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                Response::error("El campo $field es requerido.");
            }
        }

        $model = new Servicio($this->db);
        $input['panel_id'] = $panelId;

        // Handle image upload
        if (!empty($_FILES['imagen'])) {
            $input['imagen'] = Upload::imagen($_FILES['imagen'], __DIR__ . '/../uploads');
        }

        $id = $model->create($input);
        Response::success(['id' => $id], 'Servicio creado.', 201);
    }

    public function update(int $id, int $panelId): void {
        Auth::check('panel');
        $input = json_decode(file_get_contents('php://input'), true);
        $model = new Servicio($this->db);

        $servicio = $model->findById($id, $panelId);
        if (!$servicio) Response::error('Servicio no encontrado.', 404);

        $data = [];
        $allowed = ['nombre', 'precio_usd', 'duracion_dias', 'perfiles_por_cuenta', 'descripcion', 'estado', 'imap_correo'];
        foreach ($allowed as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

        if (!empty($input['imap_password'])) {
            $data['imap_password_enc'] = Crypto::encrypt($input['imap_password']);
        }

        $model->update($id, $data);
        Response::success(null, 'Servicio actualizado.');
    }

    public function delete(int $id, int $panelId): void {
        Auth::check('panel');
        $model = new Servicio($this->db);
        $model->toggleEstado($id, $panelId);
        Response::success(null, 'Estado cambiado.');
    }

    public function listaPrecios(int $panelId): void {
        $model = new Servicio($this->db);
        $servicios = $model->activos($panelId);

        $lista = array_map(function ($s) {
            return [
                'nombre'      => $s['nombre'],
                'precio_usd'  => (float) $s['precio_usd'],
                'disponibles' => (int) $s['disponibles'],
                'duracion'    => (int) $s['duracion_dias'],
            ];
        }, $servicios);

        Response::success($lista);
    }
}
