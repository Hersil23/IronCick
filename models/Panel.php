<?php

class Panel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM paneles WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findBySubdominio(string $subdominio): ?array {
        $stmt = $this->db->prepare("SELECT * FROM paneles WHERE subdominio = ? AND deleted_at IS NULL");
        $stmt->execute([$subdominio]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM paneles WHERE email = ? AND deleted_at IS NULL");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function all(string $search = '', string $estado = ''): array {
        $sql = "SELECT * FROM paneles WHERE deleted_at IS NULL";
        $params = [];

        if ($search) {
            $sql .= " AND (nombre LIKE ? OR subdominio LIKE ? OR email LIKE ?)";
            $like = "%$search%";
            $params = array_merge($params, [$like, $like, $like]);
        }
        if ($estado) {
            $sql .= " AND estado = ?";
            $params[] = $estado;
        }

        $sql .= " ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO paneles (nombre, nombre_contacto, apellido_contacto, telefono, subdominio, email, password, plan, estado, trial_expira, creditos, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW(), NOW())"
        );
        $stmt->execute([
            $data['nombre'],
            $data['nombre_contacto'] ?? null,
            $data['apellido_contacto'] ?? null,
            $data['telefono'] ?? null,
            $data['subdominio'],
            $data['email'],
            $data['password'],
            $data['plan'] ?? 'estandar',
            $data['estado'] ?? 'activo',
            $data['trial_expira'] ?? date('Y-m-d H:i:s', strtotime('+' . TRIAL_DAYS . ' days')),
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

        $stmt = $this->db->prepare("UPDATE paneles SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    public function softDelete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE paneles SET deleted_at = NOW(), estado = 'eliminado' WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateCreditos(int $id, float $monto): bool {
        $stmt = $this->db->prepare("UPDATE paneles SET creditos = creditos + ? WHERE id = ?");
        return $stmt->execute([$monto, $id]);
    }

    public function count(string $estado = ''): int {
        $sql = "SELECT COUNT(*) FROM paneles WHERE deleted_at IS NULL";
        $params = [];
        if ($estado) {
            $sql .= " AND estado = ?";
            $params[] = $estado;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
