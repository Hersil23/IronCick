<?php

require_once __DIR__ . '/../models/Perfil.php';

class PerfilesController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function updatePin(int $id): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $pin = $input['pin'] ?? '';

        $model = new Perfil($this->db);
        $model->updatePin($id, $pin);

        Response::success(null, 'PIN actualizado.');
    }
}
