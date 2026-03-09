<?php

class Servicio {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findById(int $id, int $panelId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM servicios WHERE id = ? AND panel_id = ?");
        $stmt->execute([$id, $panelId]);
        return $stmt->fetch() ?: null;
    }

    public function all(int $panelId): array {
        $stmt = $this->db->prepare(
            "SELECT s.*,
                (SELECT COUNT(*) FROM cuentas c WHERE c.servicio_id = s.id AND c.estado = 'activa') as total_cuentas,
                (SELECT COUNT(*) FROM perfiles p JOIN cuentas c ON p.cuenta_id = c.id WHERE c.servicio_id = s.id AND p.estado = 'disponible') as disponibles,
                (SELECT COUNT(*) FROM perfiles p JOIN cuentas c ON p.cuenta_id = c.id WHERE c.servicio_id = s.id AND p.estado = 'vendido') as vendidos
             FROM servicios s WHERE s.panel_id = ? ORDER BY s.nombre"
        );
        $stmt->execute([$panelId]);
        return $stmt->fetchAll();
    }

    public function activos(int $panelId): array {
        $stmt = $this->db->prepare(
            "SELECT s.*,
                (SELECT COUNT(*) FROM perfiles p JOIN cuentas c ON p.cuenta_id = c.id WHERE c.servicio_id = s.id AND p.estado = 'disponible') as disponibles
             FROM servicios s WHERE s.panel_id = ? AND s.estado = 'activo' ORDER BY s.nombre"
        );
        $stmt->execute([$panelId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO servicios (panel_id, nombre, precio_usd, duracion_dias, perfiles_por_cuenta, descripcion, imagen, imap_correo, imap_password_enc, estado, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo', NOW())"
        );
        $stmt->execute([
            $data['panel_id'],
            $data['nombre'],
            $data['precio_usd'],
            $data['duracion_dias'],
            $data['perfiles_por_cuenta'],
            $data['descripcion'] ?? '',
            $data['imagen'] ?? null,
            $data['imap_correo'] ?? null,
            !empty($data['imap_password']) ? Crypto::encrypt($data['imap_password']) : null,
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

        $stmt = $this->db->prepare("UPDATE servicios SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    public function toggleEstado(int $id, int $panelId): bool {
        $stmt = $this->db->prepare(
            "UPDATE servicios SET estado = IF(estado = 'activo', 'inactivo', 'activo') WHERE id = ? AND panel_id = ?"
        );
        return $stmt->execute([$id, $panelId]);
    }
}
