<?php

class Usuario {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findById(int $id, int $panelId = 0): ?array {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $params = [$id];
        if ($panelId) {
            $sql .= " AND panel_id = ?";
            $params[] = $panelId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email, int $panelId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? AND panel_id = ?");
        $stmt->execute([$email, $panelId]);
        return $stmt->fetch() ?: null;
    }

    public function all(int $panelId, string $rol = '', int $padreId = 0, string $search = ''): array {
        $sql = "SELECT * FROM usuarios WHERE panel_id = ?";
        $params = [$panelId];

        if ($rol) {
            $sql .= " AND rol = ?";
            $params[] = $rol;
        }
        if ($padreId) {
            $sql .= " AND padre_id = ?";
            $params[] = $padreId;
        }
        if ($search) {
            $sql .= " AND (nombre LIKE ? OR email LIKE ? OR telefono LIKE ?)";
            $like = "%$search%";
            $params = array_merge($params, [$like, $like, $like]);
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios (panel_id, rol, nombre, email, password, telefono, padre_id, creditos, estado, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, 0, 'activo', NOW(), NOW())"
        );
        $stmt->execute([
            $data['panel_id'],
            $data['rol'],
            $data['nombre'],
            $data['email'],
            password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]),
            $data['telefono'] ?? '',
            $data['padre_id'] ?? null,
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

        $stmt = $this->db->prepare("UPDATE usuarios SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    public function delete(int $id, int $panelId): bool {
        $this->db->prepare("UPDATE clientes SET vendedor_id = NULL WHERE vendedor_id = ? AND panel_id = ?")
                 ->execute([$id, $panelId]);
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ? AND panel_id = ?");
        return $stmt->execute([$id, $panelId]);
    }

    public function countClientes(int $usuarioId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM clientes WHERE vendedor_id = ? AND deleted_at IS NULL");
        $stmt->execute([$usuarioId]);
        return (int) $stmt->fetchColumn();
    }

    public function ventasMes(int $usuarioId): array {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as cantidad, COALESCE(SUM(precio_usd), 0) as monto
             FROM ventas WHERE vendedor_id = ? AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())"
        );
        $stmt->execute([$usuarioId]);
        return $stmt->fetch();
    }

    public function updateCreditos(int $id, float $monto): bool {
        $stmt = $this->db->prepare("UPDATE usuarios SET creditos = creditos + ? WHERE id = ?");
        return $stmt->execute([$monto, $id]);
    }
}
