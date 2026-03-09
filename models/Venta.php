<?php

class Venta {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findById(int $id, int $panelId): ?array {
        $stmt = $this->db->prepare(
            "SELECT v.*, s.nombre as servicio_nombre, c.correo as cuenta_correo,
                    cl.nombre as cliente_nombre, cl.apellido as cliente_apellido, cl.telefono as cliente_telefono,
                    p.numero_perfil, p.pin, u.nombre as vendedor_nombre
             FROM ventas v
             JOIN servicios s ON v.servicio_id = s.id
             JOIN cuentas c ON v.cuenta_id = c.id
             JOIN clientes cl ON v.cliente_id = cl.id
             JOIN perfiles p ON v.perfil_id = p.id
             LEFT JOIN usuarios u ON v.vendedor_id = u.id
             WHERE v.id = ? AND v.panel_id = ?"
        );
        $stmt->execute([$id, $panelId]);
        $venta = $stmt->fetch() ?: null;
        if ($venta) {
            $cuenta = $this->db->prepare("SELECT password_enc FROM cuentas WHERE id = ?");
            $cuenta->execute([$venta['cuenta_id']]);
            $enc = $cuenta->fetchColumn();
            $venta['cuenta_password'] = $enc ? Crypto::decrypt($enc) : '';
        }
        return $venta;
    }

    public function all(int $panelId, array $filtros = [], int $limit = ITEMS_PER_PAGE, int $offset = 0): array {
        $sql = "SELECT v.*, s.nombre as servicio_nombre, c.correo as cuenta_correo,
                       cl.nombre as cliente_nombre, cl.apellido as cliente_apellido, cl.telefono as cliente_telefono,
                       cl.creditos as saldo_cliente,
                       p.numero_perfil, p.pin, u.nombre as vendedor_nombre
                FROM ventas v
                JOIN servicios s ON v.servicio_id = s.id
                JOIN cuentas c ON v.cuenta_id = c.id
                JOIN clientes cl ON v.cliente_id = cl.id
                JOIN perfiles p ON v.perfil_id = p.id
                LEFT JOIN usuarios u ON v.vendedor_id = u.id
                WHERE v.panel_id = ?";
        $params = [$panelId];

        if (!empty($filtros['tab'])) {
            switch ($filtros['tab']) {
                case 'activas':
                    $sql .= " AND v.estado = 'activa'";
                    break;
                case 'por_vencer':
                    $sql .= " AND v.estado = 'activa' AND v.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
                    break;
                case 'vencidas':
                    $sql .= " AND v.estado = 'vencida'";
                    break;
                case 'vencidas_7':
                    $sql .= " AND v.estado = 'vencida' AND v.fecha_vencimiento < DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                    break;
            }
        }

        if (!empty($filtros['servicio_id'])) {
            $sql .= " AND v.servicio_id = ?";
            $params[] = $filtros['servicio_id'];
        }
        if (!empty($filtros['vendedor_id'])) {
            $sql .= " AND v.vendedor_id = ?";
            $params[] = $filtros['vendedor_id'];
        }
        if (!empty($filtros['fecha'])) {
            $sql .= " AND DATE(v.fecha_compra) = ?";
            $params[] = $filtros['fecha'];
        }
        if (!empty($filtros['q'])) {
            $sql .= " AND (cl.nombre LIKE ? OR cl.telefono LIKE ? OR s.nombre LIKE ? OR c.correo LIKE ? OR p.pin LIKE ? OR v.numero_orden LIKE ?)";
            $like = "%" . $filtros['q'] . "%";
            $params = array_merge($params, [$like, $like, $like, $like, $like, $like]);
        }

        $sql .= " ORDER BY v.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(int $panelId, array $filtros = []): int {
        $sql = "SELECT COUNT(*) FROM ventas v
                JOIN servicios s ON v.servicio_id = s.id
                JOIN cuentas c ON v.cuenta_id = c.id
                JOIN clientes cl ON v.cliente_id = cl.id
                JOIN perfiles p ON v.perfil_id = p.id
                WHERE v.panel_id = ?";
        $params = [$panelId];

        if (!empty($filtros['tab'])) {
            switch ($filtros['tab']) {
                case 'activas':    $sql .= " AND v.estado = 'activa'"; break;
                case 'por_vencer': $sql .= " AND v.estado = 'activa' AND v.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)"; break;
                case 'vencidas':   $sql .= " AND v.estado = 'vencida'"; break;
                case 'vencidas_7': $sql .= " AND v.estado = 'vencida' AND v.fecha_vencimiento < DATE_SUB(CURDATE(), INTERVAL 7 DAY)"; break;
            }
        }
        if (!empty($filtros['servicio_id'])) { $sql .= " AND v.servicio_id = ?"; $params[] = $filtros['servicio_id']; }
        if (!empty($filtros['vendedor_id'])) { $sql .= " AND v.vendedor_id = ?"; $params[] = $filtros['vendedor_id']; }
        if (!empty($filtros['q'])) {
            $sql .= " AND (cl.nombre LIKE ? OR cl.telefono LIKE ? OR s.nombre LIKE ? OR c.correo LIKE ? OR p.pin LIKE ? OR v.numero_orden LIKE ?)";
            $like = "%" . $filtros['q'] . "%";
            $params = array_merge($params, [$like, $like, $like, $like, $like, $like]);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function tabs(int $panelId): array {
        $stmt = $this->db->prepare("SELECT
            COUNT(*) as todas,
            SUM(estado = 'activa') as activas,
            SUM(estado = 'activa' AND fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)) as por_vencer,
            SUM(estado = 'vencida') as vencidas,
            SUM(estado = 'vencida' AND fecha_vencimiento < DATE_SUB(CURDATE(), INTERVAL 7 DAY)) as vencidas_7
            FROM ventas WHERE panel_id = ?");
        $stmt->execute([$panelId]);
        return $stmt->fetch();
    }

    public function crear(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO ventas (panel_id, cliente_id, vendedor_id, servicio_id, cuenta_id, perfil_id, numero_orden, precio_usd, precio_local, fecha_compra, fecha_vencimiento, tipo, estado, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? DAY), ?, 'activa', NOW(), NOW())"
        );
        $stmt->execute([
            $data['panel_id'],
            $data['cliente_id'],
            $data['vendedor_id'] ?? null,
            $data['servicio_id'],
            $data['cuenta_id'],
            $data['perfil_id'],
            $data['numero_orden'],
            $data['precio_usd'],
            $data['precio_local'] ?? 0,
            $data['duracion_dias'],
            $data['tipo'] ?? 'nueva',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function renovar(int $id, int $panelId, int $duracionDias): bool {
        $stmt = $this->db->prepare(
            "UPDATE ventas SET fecha_vencimiento = DATE_ADD(GREATEST(fecha_vencimiento, CURDATE()), INTERVAL ? DAY),
             tipo = 'renovacion', estado = 'activa', updated_at = NOW()
             WHERE id = ? AND panel_id = ?"
        );
        return $stmt->execute([$duracionDias, $id, $panelId]);
    }

    public function porCobrar(int $panelId, int $dias = 3): array {
        $stmt = $this->db->prepare(
            "SELECT v.*, s.nombre as servicio_nombre, cl.nombre as cliente_nombre, cl.telefono as cliente_telefono
             FROM ventas v
             JOIN servicios s ON v.servicio_id = s.id
             JOIN clientes cl ON v.cliente_id = cl.id
             WHERE v.panel_id = ? AND v.estado = 'activa'
             AND v.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
             ORDER BY v.fecha_vencimiento"
        );
        $stmt->execute([$panelId, $dias]);
        return $stmt->fetchAll();
    }

    public function byCliente(int $clienteId, int $panelId): array {
        $stmt = $this->db->prepare(
            "SELECT v.*, s.nombre as servicio_nombre, c.correo as cuenta_correo, p.numero_perfil, p.pin
             FROM ventas v
             JOIN servicios s ON v.servicio_id = s.id
             JOIN cuentas c ON v.cuenta_id = c.id
             JOIN perfiles p ON v.perfil_id = p.id
             WHERE v.cliente_id = ? AND v.panel_id = ?
             ORDER BY v.created_at DESC"
        );
        $stmt->execute([$clienteId, $panelId]);
        return $stmt->fetchAll();
    }

    public function delete(int $id, int $panelId): bool {
        $venta = $this->findById($id, $panelId);
        if ($venta) {
            $this->db->prepare("UPDATE perfiles SET estado = 'disponible' WHERE id = ?")
                     ->execute([$venta['perfil_id']]);
        }
        $stmt = $this->db->prepare("DELETE FROM ventas WHERE id = ? AND panel_id = ?");
        return $stmt->execute([$id, $panelId]);
    }

    public function actualizarVencidas(): int {
        $stmt = $this->db->prepare(
            "UPDATE ventas SET estado = 'vencida', updated_at = NOW()
             WHERE estado = 'activa' AND fecha_vencimiento < CURDATE()"
        );
        $stmt->execute();
        return $stmt->rowCount();
    }
}
