<?php

class Cuenta {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findById(int $id, int $panelId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM cuentas WHERE id = ? AND panel_id = ?");
        $stmt->execute([$id, $panelId]);
        $cuenta = $stmt->fetch() ?: null;
        if ($cuenta) {
            $cuenta['password'] = Crypto::decrypt($cuenta['password_enc']);
        }
        return $cuenta;
    }

    public function all(int $panelId, int $servicioId = 0, string $estado = '', string $search = '', int $limit = ITEMS_PER_PAGE, int $offset = 0): array {
        $sql = "SELECT c.*, s.nombre as servicio_nombre, p.nombre as proveedor_nombre,
                (SELECT COUNT(*) FROM perfiles pf WHERE pf.cuenta_id = c.id AND pf.estado = 'vendido') as vendidos,
                (SELECT COUNT(*) FROM perfiles pf WHERE pf.cuenta_id = c.id AND pf.estado = 'disponible') as disponibles,
                (SELECT COUNT(*) FROM perfiles pf WHERE pf.cuenta_id = c.id) as total_perfiles
                FROM cuentas c
                LEFT JOIN servicios s ON c.servicio_id = s.id
                LEFT JOIN proveedores p ON c.proveedor_id = p.id
                WHERE c.panel_id = ?";
        $params = [$panelId];

        if ($servicioId) {
            $sql .= " AND c.servicio_id = ?";
            $params[] = $servicioId;
        }
        if ($estado) {
            $sql .= " AND c.estado = ?";
            $params[] = $estado;
        }
        if ($search) {
            $sql .= " AND (c.correo LIKE ? OR s.nombre LIKE ? OR p.nombre LIKE ?)";
            $like = "%$search%";
            $params = array_merge($params, [$like, $like, $like]);
        }

        $sql .= " ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $cuentas = $stmt->fetchAll();

        foreach ($cuentas as &$c) {
            $c['password'] = Crypto::decrypt($c['password_enc']);
        }
        return $cuentas;
    }

    public function count(int $panelId, int $servicioId = 0, string $estado = '', string $search = ''): int {
        $sql = "SELECT COUNT(*) FROM cuentas c
                LEFT JOIN servicios s ON c.servicio_id = s.id
                LEFT JOIN proveedores p ON c.proveedor_id = p.id
                WHERE c.panel_id = ?";
        $params = [$panelId];

        if ($servicioId) {
            $sql .= " AND c.servicio_id = ?";
            $params[] = $servicioId;
        }
        if ($estado) {
            $sql .= " AND c.estado = ?";
            $params[] = $estado;
        }
        if ($search) {
            $sql .= " AND (c.correo LIKE ? OR s.nombre LIKE ? OR p.nombre LIKE ?)";
            $like = "%$search%";
            $params = array_merge($params, [$like, $like, $like]);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO cuentas (panel_id, servicio_id, proveedor_id, correo, password_enc, costo_usd, fecha_vencimiento, estado, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'activa', NOW())"
        );
        $stmt->execute([
            $data['panel_id'],
            $data['servicio_id'],
            $data['proveedor_id'] ?? null,
            $data['correo'],
            Crypto::encrypt($data['password']),
            $data['costo_usd'],
            $data['fecha_vencimiento'],
        ]);
        $cuentaId = (int) $this->db->lastInsertId();

        $servicio = $this->db->prepare("SELECT perfiles_por_cuenta FROM servicios WHERE id = ?");
        $servicio->execute([$data['servicio_id']]);
        $perfilesPorCuenta = (int) $servicio->fetchColumn();

        for ($i = 1; $i <= $perfilesPorCuenta; $i++) {
            $this->db->prepare(
                "INSERT INTO perfiles (cuenta_id, numero_perfil, pin, estado) VALUES (?, ?, '', 'disponible')"
            )->execute([$cuentaId, $i]);
        }

        return $cuentaId;
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            if ($key === 'password') {
                $fields[] = "password_enc = ?";
                $params[] = Crypto::encrypt($value);
            } else {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        $params[] = $id;

        $stmt = $this->db->prepare("UPDATE cuentas SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($params);
    }

    public function delete(int $id, int $panelId): bool {
        $stmt = $this->db->prepare("DELETE FROM cuentas WHERE id = ? AND panel_id = ?");
        return $stmt->execute([$id, $panelId]);
    }

    public function porVencer(int $panelId, int $dias = 7): array {
        $stmt = $this->db->prepare(
            "SELECT c.*, s.nombre as servicio_nombre
             FROM cuentas c
             JOIN servicios s ON c.servicio_id = s.id
             WHERE c.panel_id = ? AND c.estado = 'activa'
             AND c.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
             ORDER BY c.fecha_vencimiento"
        );
        $stmt->execute([$panelId, $dias]);
        return $stmt->fetchAll();
    }
}
