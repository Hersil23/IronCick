<?php

class Proveedor {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findById(int $id, int $panelId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM proveedores WHERE id = ? AND panel_id = ?");
        $stmt->execute([$id, $panelId]);
        return $stmt->fetch() ?: null;
    }

    public function all(int $panelId): array {
        $stmt = $this->db->prepare(
            "SELECT p.*,
                (SELECT COUNT(*) FROM cuentas c WHERE c.proveedor_id = p.id) as total_cuentas
             FROM proveedores p WHERE p.panel_id = ? ORDER BY p.nombre"
        );
        $stmt->execute([$panelId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO proveedores (panel_id, nombre, contacto, telefono, notas, estado) VALUES (?, ?, ?, ?, ?, 'activo')"
        );
        $stmt->execute([
            $data['panel_id'],
            $data['nombre'],
            $data['contacto'] ?? '',
            $data['telefono'] ?? '',
            $data['notas'] ?? '',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $id;

        $stmt = $this->db->prepare("UPDATE proveedores SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }
}
