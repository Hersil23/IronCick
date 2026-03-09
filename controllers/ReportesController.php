<?php

class ReportesController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function ventas(int $panelId): void {
        Auth::check('panel');
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');

        $stmt = $this->db->prepare(
            "SELECT DATE(v.fecha_compra) as fecha, COUNT(*) as cantidad,
                    SUM(v.precio_usd) as ingresos, v.tipo,
                    s.nombre as servicio
             FROM ventas v
             JOIN servicios s ON v.servicio_id = s.id
             WHERE v.panel_id = ? AND v.fecha_compra BETWEEN ? AND ?
             GROUP BY DATE(v.fecha_compra), v.tipo, s.nombre
             ORDER BY fecha"
        );
        $stmt->execute([$panelId, $desde, $hasta]);

        Response::success($stmt->fetchAll());
    }

    public function vendedores(int $panelId): void {
        Auth::check('panel');
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');

        $stmt = $this->db->prepare(
            "SELECT u.nombre as vendedor, COUNT(v.id) as ventas, SUM(v.precio_usd) as ingresos,
                    SUM(v.tipo = 'nueva') as nuevas, SUM(v.tipo = 'renovacion') as renovaciones
             FROM ventas v
             LEFT JOIN usuarios u ON v.vendedor_id = u.id
             WHERE v.panel_id = ? AND v.fecha_compra BETWEEN ? AND ?
             GROUP BY v.vendedor_id
             ORDER BY ingresos DESC"
        );
        $stmt->execute([$panelId, $desde, $hasta]);

        Response::success($stmt->fetchAll());
    }

    public function servicios(int $panelId): void {
        Auth::check('panel');
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');

        $stmt = $this->db->prepare(
            "SELECT s.nombre as servicio, COUNT(v.id) as ventas, SUM(v.precio_usd) as ingresos,
                    (SELECT COUNT(*) FROM perfiles p JOIN cuentas c ON p.cuenta_id = c.id WHERE c.servicio_id = s.id AND p.estado = 'vendido') as perfiles_vendidos,
                    (SELECT COUNT(*) FROM perfiles p JOIN cuentas c ON p.cuenta_id = c.id WHERE c.servicio_id = s.id AND p.estado = 'disponible') as perfiles_disponibles
             FROM servicios s
             LEFT JOIN ventas v ON v.servicio_id = s.id AND v.fecha_compra BETWEEN ? AND ?
             WHERE s.panel_id = ?
             GROUP BY s.id
             ORDER BY ingresos DESC"
        );
        $stmt->execute([$desde, $hasta, $panelId]);

        Response::success($stmt->fetchAll());
    }

    public function clientes(int $panelId): void {
        Auth::check('panel');
        $desde = $_GET['desde'] ?? date('Y-m-01');
        $hasta = $_GET['hasta'] ?? date('Y-m-d');

        // Nuevos vs renovaciones
        $stmt = $this->db->prepare(
            "SELECT
                (SELECT COUNT(DISTINCT cliente_id) FROM ventas WHERE panel_id = ? AND tipo = 'nueva' AND fecha_compra BETWEEN ? AND ?) as nuevos,
                (SELECT COUNT(DISTINCT cliente_id) FROM ventas WHERE panel_id = ? AND tipo = 'renovacion' AND fecha_compra BETWEEN ? AND ?) as renovaciones,
                (SELECT COUNT(*) FROM clientes WHERE panel_id = ? AND estado = 'suspendido' AND deleted_at IS NULL) as suspendidos,
                (SELECT COUNT(*) FROM clientes WHERE panel_id = ? AND deleted_at IS NOT NULL) as eliminados"
        );
        $stmt->execute([$panelId, $desde, $hasta, $panelId, $desde, $hasta, $panelId, $panelId]);

        Response::success($stmt->fetch());
    }
}
