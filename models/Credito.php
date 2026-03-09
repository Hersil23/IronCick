<?php

class Credito {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function registrar(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO movimientos_creditos (panel_id, tipo, origen, destino_tipo, destino_id, monto, concepto, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmt->execute([
            $data['panel_id'],
            $data['tipo'],
            $data['origen'],
            $data['destino_tipo'],
            $data['destino_id'],
            $data['monto'],
            $data['concepto'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function movimientos(int $panelId, int $limit = ITEMS_PER_PAGE, int $offset = 0): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM movimientos_creditos WHERE panel_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?"
        );
        $stmt->execute([$panelId, $limit, $offset]);
        return $stmt->fetchAll();
    }

    public function countMovimientos(int $panelId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM movimientos_creditos WHERE panel_id = ?");
        $stmt->execute([$panelId]);
        return (int) $stmt->fetchColumn();
    }
}
