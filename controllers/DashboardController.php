<?php

require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../models/Cuenta.php';
require_once __DIR__ . '/../models/Pago.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Servicio.php';

class DashboardController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function metricas(int $panelId): void {
        $periodo = $_GET['periodo'] ?? 'mes';
        $dateCondition = $this->dateCondition($periodo);

        // Ingresos
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(precio_usd), 0) as ingresos, COUNT(*) as total_ventas
             FROM ventas WHERE panel_id = ? AND $dateCondition"
        );
        $stmt->execute([$panelId]);
        $ventas = $stmt->fetch();

        // Costos
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(c.costo_usd), 0) as costos
             FROM cuentas c WHERE c.panel_id = ? AND c.estado = 'activa'"
        );
        $stmt->execute([$panelId]);
        $costos = $stmt->fetch();

        // Ventas nuevas vs renovaciones
        $stmt = $this->db->prepare(
            "SELECT tipo, COUNT(*) as cantidad, COALESCE(SUM(precio_usd), 0) as monto
             FROM ventas WHERE panel_id = ? AND $dateCondition GROUP BY tipo"
        );
        $stmt->execute([$panelId]);
        $tipos = [];
        while ($row = $stmt->fetch()) {
            $tipos[$row['tipo']] = $row;
        }

        // Clientes activos
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM clientes WHERE panel_id = ? AND estado = 'activo' AND deleted_at IS NULL");
        $stmt->execute([$panelId]);
        $clientesActivos = (int) $stmt->fetchColumn();

        // Cuentas activas
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM cuentas WHERE panel_id = ? AND estado = 'activa'");
        $stmt->execute([$panelId]);
        $cuentasActivas = (int) $stmt->fetchColumn();

        // Perfiles
        $stmt = $this->db->prepare(
            "SELECT
                SUM(p.estado = 'vendido') as vendidos,
                SUM(p.estado = 'disponible') as disponibles
             FROM perfiles p
             JOIN cuentas c ON p.cuenta_id = c.id
             WHERE c.panel_id = ?"
        );
        $stmt->execute([$panelId]);
        $perfiles = $stmt->fetch();

        // Retencion
        $stmt = $this->db->prepare(
            "SELECT COUNT(DISTINCT cliente_id) as renovaron
             FROM ventas WHERE panel_id = ? AND tipo = 'renovacion'
             AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"
        );
        $stmt->execute([$panelId]);
        $renovaron = (int) $stmt->fetchColumn();
        $retencion = $clientesActivos > 0 ? round(($renovaron / $clientesActivos) * 100, 1) : 0;

        // Inversion activa
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(costo_usd), 0) as inversion FROM cuentas WHERE panel_id = ? AND estado = 'activa'"
        );
        $stmt->execute([$panelId]);
        $inversion = (float) $stmt->fetchColumn();

        Response::success([
            'ingresos'         => (float) $ventas['ingresos'],
            'costos'           => (float) $costos['costos'],
            'utilidad'         => (float) $ventas['ingresos'] - (float) $costos['costos'],
            'inversion_activa' => $inversion,
            'ventas_nuevas'    => $tipos['nueva'] ?? ['cantidad' => 0, 'monto' => 0],
            'renovaciones'     => $tipos['renovacion'] ?? ['cantidad' => 0, 'monto' => 0],
            'clientes_activos' => $clientesActivos,
            'cuentas_activas'  => $cuentasActivas,
            'perfiles_vendidos'    => (int) ($perfiles['vendidos'] ?? 0),
            'perfiles_disponibles' => (int) ($perfiles['disponibles'] ?? 0),
            'retencion'        => $retencion,
        ]);
    }

    public function stock(int $panelId): void {
        $stmt = $this->db->prepare(
            "SELECT s.id, s.nombre,
                COUNT(DISTINCT c.id) as cuentas,
                COUNT(p.id) as perfiles,
                SUM(p.estado = 'vendido') as vendidos,
                SUM(p.estado = 'disponible') as disponibles,
                COALESCE(SUM(c2.costo_usd), 0) as costo,
                s.precio_usd
             FROM servicios s
             LEFT JOIN cuentas c ON c.servicio_id = s.id AND c.estado = 'activa'
             LEFT JOIN perfiles p ON p.cuenta_id = c.id
             LEFT JOIN cuentas c2 ON c2.id = c.id
             WHERE s.panel_id = ? AND s.estado = 'activo'
             GROUP BY s.id
             ORDER BY s.nombre"
        );
        $stmt->execute([$panelId]);
        $stock = $stmt->fetchAll();

        foreach ($stock as &$s) {
            $total = (int) $s['perfiles'];
            $vendidos = (int) $s['vendidos'];
            $s['ocupacion'] = $total > 0 ? round(($vendidos / $total) * 100, 1) : 0;
            $s['ingreso_potencial'] = (int) $s['disponibles'] * (float) $s['precio_usd'];
            $s['ganancia_neta'] = $s['ingreso_potencial'] - (float) $s['costo'];
        }

        Response::success($stock);
    }

    public function ventasCobrar(int $panelId): void {
        $ventaModel = new Venta($this->db);
        Response::success($ventaModel->porCobrar($panelId, 3));
    }

    public function cuentasVencer(int $panelId): void {
        $cuentaModel = new Cuenta($this->db);
        Response::success($cuentaModel->porVencer($panelId, 7));
    }

    public function ultimosPagos(int $panelId): void {
        $pagoModel = new Pago($this->db);
        Response::success($pagoModel->ultimos($panelId, 10));
    }

    private function dateCondition(string $periodo): string {
        return match ($periodo) {
            'hoy'    => "DATE(created_at) = CURDATE()",
            'semana' => "YEARWEEK(created_at) = YEARWEEK(NOW())",
            'mes'    => "MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())",
            'ano'    => "YEAR(created_at) = YEAR(NOW())",
            default  => "MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())",
        };
    }
}
