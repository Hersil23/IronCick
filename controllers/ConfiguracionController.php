<?php

require_once __DIR__ . '/../models/Configuracion.php';

class ConfiguracionController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function get(int $panelId): void {
        Auth::check('panel');
        $model = new Configuracion($this->db);
        $config = $model->get($panelId);

        if (!$config) {
            $model->defaults($panelId);
            $config = $model->get($panelId);
        }

        Response::success($config);
    }

    public function save(int $panelId): void {
        Auth::check('panel');
        $input = json_decode(file_get_contents('php://input'), true);

        $allowed = [
            'moneda', 'tasa_fuente', 'tasa_manual', 'margen_recargo',
            'banco', 'telefono_pago', 'cuenta_banco', 'dias_suspension',
            'whatsapp_contacto',
            'mensaje_entrega', 'mensaje_cambio', 'mensaje_cobro',
            'mensaje_cobro_grupal', 'mensaje_renovacion', 'mensaje_renovacion_grupal',
            'mensaje_alerta_3dias', 'mensaje_alerta_1dia', 'mensaje_alerta_hoy',
            'mensaje_bienvenida', 'mensaje_recarga', 'mensaje_imap',
            'tienda_colores_json', 'tienda_descripcion', 'tienda_bienvenida',
            'tienda_faq_json', 'tienda_redes_json',
        ];

        $data = [];
        foreach ($allowed as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

        $model = new Configuracion($this->db);
        $model->save($panelId, $data);

        Response::success(null, 'Configuracion guardada.');
    }

    public function uploadLogo(int $panelId): void {
        Auth::check('panel');

        if (empty($_FILES['logo'])) {
            Response::error('No se recibio archivo.');
        }

        $nombre = Upload::imagen($_FILES['logo'], __DIR__ . '/../uploads');

        $model = new Configuracion($this->db);
        $model->save($panelId, ['tienda_logo' => $nombre]);

        Response::success(['logo' => $nombre], 'Logo subido.');
    }
}
