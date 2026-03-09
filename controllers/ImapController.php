<?php

require_once __DIR__ . '/../models/Servicio.php';

class ImapController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function buscarCodigo(int $panelId): void {
        Auth::check('panel');
        $input = json_decode(file_get_contents('php://input'), true);
        $servicioId = (int) ($input['servicio_id'] ?? 0);

        if (!$servicioId) Response::error('Servicio requerido.');

        $servicioModel = new Servicio($this->db);
        $servicio = $servicioModel->findById($servicioId, $panelId);
        if (!$servicio) Response::error('Servicio no encontrado.', 404);

        if (empty($servicio['imap_correo']) || empty($servicio['imap_password_enc'])) {
            Response::error('Este servicio no tiene IMAP configurado. Ve a Servicios > Editar para configurar el correo IMAP y la contrasena de aplicacion.');
        }

        $email = $servicio['imap_correo'];
        $password = Crypto::decrypt($servicio['imap_password_enc']);

        // Detect server
        $host = '';
        if (str_contains($email, 'gmail')) {
            $host = '{imap.gmail.com:993/imap/ssl}INBOX';
        } elseif (str_contains($email, 'outlook') || str_contains($email, 'hotmail')) {
            $host = '{outlook.office365.com:993/imap/ssl}INBOX';
        } else {
            $domain = substr($email, strpos($email, '@') + 1);
            $host = "{imap.$domain:993/imap/ssl}INBOX";
        }

        $inbox = @imap_open($host, $email, $password);
        if (!$inbox) {
            Response::error('No se pudo conectar al servidor IMAP. Verifica las credenciales y que la contrasena de aplicacion sea correcta.');
        }

        // Search last 5 emails
        $emails = imap_search($inbox, 'ALL', SE_UID);
        if (!$emails) {
            imap_close($inbox);
            Response::error('No se encontraron correos.');
        }

        rsort($emails);
        $codigo = null;

        for ($i = 0; $i < min(5, count($emails)); $i++) {
            $body = imap_fetchbody($inbox, $emails[$i], 1, FT_UID);
            $body = quoted_printable_decode($body);

            // Search for verification codes (4-8 digits)
            if (preg_match('/\b(\d{4,8})\b/', $body, $matches)) {
                $codigo = $matches[1];
                break;
            }
        }

        imap_close($inbox);

        if (!$codigo) {
            Response::error('No se encontro un codigo de verificacion reciente.');
        }

        // Save to history
        $stmt = $this->db->prepare(
            "INSERT INTO historial_imap (panel_id, servicio_id, cliente_id, codigo, enviado_whatsapp, created_at)
             VALUES (?, ?, ?, ?, 0, NOW())"
        );
        $stmt->execute([$panelId, $servicioId, $input['cliente_id'] ?? null, $codigo]);

        Response::success(['codigo' => $codigo], 'Codigo encontrado.');
    }

    public function historial(int $panelId): void {
        Auth::check('panel');
        $stmt = $this->db->prepare(
            "SELECT h.*, s.nombre as servicio_nombre, c.nombre as cliente_nombre
             FROM historial_imap h
             LEFT JOIN servicios s ON h.servicio_id = s.id
             LEFT JOIN clientes c ON h.cliente_id = c.id
             WHERE h.panel_id = ?
             ORDER BY h.created_at DESC LIMIT 50"
        );
        $stmt->execute([$panelId]);
        Response::success($stmt->fetchAll());
    }
}
