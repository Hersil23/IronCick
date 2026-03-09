<?php

class Cliente {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findById(int $id, int $panelId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM clientes WHERE id = ? AND panel_id = ? AND deleted_at IS NULL");
        $stmt->execute([$id, $panelId]);
        return $stmt->fetch() ?: null;
    }

    public function all(int $panelId, string $search = '', string $estado = '', int $vendedorId = 0, int $limit = ITEMS_PER_PAGE, int $offset = 0): array {
        $sql = "SELECT c.*,
                (SELECT COUNT(*) FROM ventas v WHERE v.cliente_id = c.id AND v.estado = 'activa') as servicios_activos
                FROM clientes c WHERE c.panel_id = ? AND c.deleted_at IS NULL";
        $params = [$panelId];

        if ($search) {
            $sql .= " AND (c.nombre LIKE ? OR c.apellido LIKE ? OR c.telefono LIKE ? OR c.email LIKE ?)";
            $like = "%$search%";
            $params = array_merge($params, [$like, $like, $like, $like]);
        }
        if ($estado) {
            $sql .= " AND c.estado = ?";
            $params[] = $estado;
        }
        if ($vendedorId) {
            $sql .= " AND c.vendedor_id = ?";
            $params[] = $vendedorId;
        }

        $sql .= " ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(int $panelId, string $search = '', string $estado = '', int $vendedorId = 0): int {
        $sql = "SELECT COUNT(*) FROM clientes WHERE panel_id = ? AND deleted_at IS NULL";
        $params = [$panelId];

        if ($search) {
            $sql .= " AND (nombre LIKE ? OR apellido LIKE ? OR telefono LIKE ? OR email LIKE ?)";
            $like = "%$search%";
            $params = array_merge($params, [$like, $like, $like, $like]);
        }
        if ($estado) {
            $sql .= " AND estado = ?";
            $params[] = $estado;
        }
        if ($vendedorId) {
            $sql .= " AND vendedor_id = ?";
            $params[] = $vendedorId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO clientes (panel_id, vendedor_id, nombre, apellido, email, telefono, creditos, estado, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, 0, 'activo', NOW(), NOW())"
        );
        $stmt->execute([
            $data['panel_id'],
            $data['vendedor_id'] ?? null,
            $data['nombre'],
            $data['apellido'] ?? '',
            $data['email'] ?? '',
            $data['telefono'],
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
        $fields[] = "updated_at = NOW()";
        $params[] = $id;

        $stmt = $this->db->prepare("UPDATE clientes SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    public function updateEstado(int $id, string $estado): bool {
        $stmt = $this->db->prepare("UPDATE clientes SET estado = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$estado, $id]);
    }

    public function softDelete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE clientes SET deleted_at = NOW(), estado = 'eliminado' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateCreditos(int $id, float $monto): bool {
        $stmt = $this->db->prepare("UPDATE clientes SET creditos = creditos + ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$monto, $id]);
    }

    public function getCreditos(int $id): float {
        $stmt = $this->db->prepare("SELECT creditos FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return (float) $stmt->fetchColumn();
    }
}
