<?php

class Perfil {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM perfiles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function byCuenta(int $cuentaId): array {
        $stmt = $this->db->prepare(
            "SELECT p.*, v.id as venta_id, cl.nombre as cliente_nombre, cl.telefono as cliente_telefono
             FROM perfiles p
             LEFT JOIN ventas v ON v.perfil_id = p.id AND v.estado = 'activa'
             LEFT JOIN clientes cl ON v.cliente_id = cl.id
             WHERE p.cuenta_id = ?
             ORDER BY p.numero_perfil"
        );
        $stmt->execute([$cuentaId]);
        return $stmt->fetchAll();
    }

    public function disponible(int $servicioId, int $panelId): ?array {
        $stmt = $this->db->prepare(
            "SELECT p.*, c.id as cuenta_id, c.correo, c.password_enc
             FROM perfiles p
             JOIN cuentas c ON p.cuenta_id = c.id
             WHERE c.servicio_id = ? AND c.panel_id = ? AND c.estado = 'activa'
             AND p.estado = 'disponible'
             ORDER BY c.fecha_vencimiento DESC, p.numero_perfil
             LIMIT 1
             FOR UPDATE"
        );
        $stmt->execute([$servicioId, $panelId]);
        $perfil = $stmt->fetch() ?: null;
        if ($perfil) {
            $perfil['password'] = Crypto::decrypt($perfil['password_enc']);
        }
        return $perfil;
    }

    public function marcarVendido(int $id): bool {
        $stmt = $this->db->prepare("UPDATE perfiles SET estado = 'vendido' WHERE id = ? AND estado = 'disponible'");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public function marcarDisponible(int $id): bool {
        $stmt = $this->db->prepare("UPDATE perfiles SET estado = 'disponible' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updatePin(int $id, string $pin): bool {
        $stmt = $this->db->prepare("UPDATE perfiles SET pin = ? WHERE id = ?");
        return $stmt->execute([$pin, $id]);
    }
}
