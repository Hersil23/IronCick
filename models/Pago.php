<?php

class Pago {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO pagos (panel_id, cliente_id, venta_id, monto_usd, monto_local, tasa_cambio, metodo, comprobante_url, estado, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pendiente', NOW())"
        );
        $stmt->execute([
            $data['panel_id'],
            $data['cliente_id'],
            $data['venta_id'] ?? null,
            $data['monto_usd'],
            $data['monto_local'] ?? 0,
            $data['tasa_cambio'] ?? 1,
            $data['metodo'] ?? '',
            $data['comprobante_url'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function confirmar(int $id, int $confirmadoPor): bool {
        $stmt = $this->db->prepare(
            "UPDATE pagos SET estado = 'confirmado', confirmado_por = ? WHERE id = ?"
        );
        return $stmt->execute([$confirmadoPor, $id]);
    }

    public function ultimos(int $panelId, int $limit = 10): array {
        $stmt = $this->db->prepare(
            "SELECT p.*, cl.nombre as cliente_nombre, s.nombre as servicio_nombre, v.tipo as venta_tipo
             FROM pagos p
             JOIN clientes cl ON p.cliente_id = cl.id
             LEFT JOIN ventas v ON p.venta_id = v.id
             LEFT JOIN servicios s ON v.servicio_id = s.id
             WHERE p.panel_id = ? AND p.estado = 'confirmado'
             ORDER BY p.created_at DESC LIMIT ?"
        );
        $stmt->execute([$panelId, $limit]);
        return $stmt->fetchAll();
    }

    public function pendientes(int $panelId): array {
        $stmt = $this->db->prepare(
            "SELECT p.*, cl.nombre as cliente_nombre
             FROM pagos p
             JOIN clientes cl ON p.cliente_id = cl.id
             WHERE p.panel_id = ? AND p.estado = 'pendiente'
             ORDER BY p.created_at DESC"
        );
        $stmt->execute([$panelId]);
        return $stmt->fetchAll();
    }
}
