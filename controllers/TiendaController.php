<?php

require_once __DIR__ . '/../models/Configuracion.php';
require_once __DIR__ . '/../models/Servicio.php';
require_once __DIR__ . '/../models/Venta.php';

class TiendaController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function landing(array $panel): void {
        $configModel = new Configuracion($this->db);
        $config = $configModel->get($panel['id']);

        $servicioModel = new Servicio($this->db);
        $servicios = $servicioModel->activos($panel['id']);

        $pageTitle = $panel['nombre'] . ' - Servicios Digitales';
        include __DIR__ . '/../views/tienda/landing.php';
    }

    public function catalogo(array $panel): void {
        $configModel = new Configuracion($this->db);
        $config = $configModel->get($panel['id']);

        $servicioModel = new Servicio($this->db);
        $servicios = $servicioModel->activos($panel['id']);

        $pageTitle = 'Catalogo - ' . $panel['nombre'];
        include __DIR__ . '/../views/tienda/catalogo.php';
    }

    public function comoComprar(array $panel): void {
        $configModel = new Configuracion($this->db);
        $config = $configModel->get($panel['id']);

        $pageTitle = 'Como Comprar - ' . $panel['nombre'];
        include __DIR__ . '/../views/tienda/como-comprar.php';
    }

    public function faq(array $panel): void {
        $configModel = new Configuracion($this->db);
        $config = $configModel->get($panel['id']);
        $faqs = json_decode($config['tienda_faq_json'] ?? '[]', true) ?: [];

        $pageTitle = 'Preguntas Frecuentes - ' . $panel['nombre'];
        include __DIR__ . '/../views/tienda/faq.php';
    }

    public function pedido(array $panel, string $orden): void {
        $stmt = $this->db->prepare(
            "SELECT v.*, s.nombre as servicio_nombre
             FROM ventas v
             JOIN servicios s ON v.servicio_id = s.id
             WHERE v.numero_orden = ? AND v.panel_id = ?"
        );
        $stmt->execute([sanitize($orden), $panel['id']]);
        $venta = $stmt->fetch() ?: null;

        $pageTitle = 'Seguimiento de Pedido - ' . $panel['nombre'];
        include __DIR__ . '/../views/tienda/pedido.php';
    }

    public function miCuenta(array $panel): void {
        $pageTitle = 'Mi Cuenta - ' . $panel['nombre'];
        include __DIR__ . '/../views/tienda/mi-cuenta.php';
    }
}
