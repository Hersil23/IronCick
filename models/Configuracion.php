<?php

class Configuracion {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function get(int $panelId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM configuracion WHERE panel_id = ?");
        $stmt->execute([$panelId]);
        return $stmt->fetch() ?: null;
    }

    public function save(int $panelId, array $data): bool {
        $existing = $this->get($panelId);

        if ($existing) {
            $fields = [];
            $params = [];
            foreach ($data as $key => $value) {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
            $params[] = $panelId;
            $stmt = $this->db->prepare("UPDATE configuracion SET " . implode(', ', $fields) . " WHERE panel_id = ?");
            return $stmt->execute($params);
        }

        $data['panel_id'] = $panelId;
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare("INSERT INTO configuracion ($columns) VALUES ($placeholders)");
        return $stmt->execute(array_values($data));
    }

    public function defaults(int $panelId): bool {
        return $this->save($panelId, [
            'moneda' => 'USD',
            'tasa_fuente' => 'binance',
            'dias_suspension' => DEFAULT_SUSPENSION_DAYS,
            'mensaje_entrega' => "Hola {cliente}, aqui tienes tu servicio de {servicio}:\n\nCuenta: {cuenta}\nContrasena: {password}\nPerfil: {perfil}\nPIN: {pin}\n\nVence: {vencimiento}\nOrden: {numero_orden}",
            'mensaje_cobro' => "Hola {cliente}, tu servicio de {servicio} vence el {vencimiento}.\n\nPrecio: {precio}\n\nDatos de pago:\nBanco: {banco}\nTelefono: {telefono_pago}\nCuenta: {cuenta_banco}",
            'mensaje_renovacion' => "Hola {cliente}, tu servicio de {servicio} ha sido renovado.\n\nNuevo vencimiento: {vencimiento}\nGracias por tu pago.",
            'mensaje_bienvenida' => "Hola {cliente}, bienvenido/a. Tu cuenta ha sido creada exitosamente.",
            'mensaje_alerta_3dias' => "Hola {cliente}, tu servicio de {servicio} vence en 3 dias ({vencimiento}). Renueva a tiempo.",
            'mensaje_alerta_1dia' => "Hola {cliente}, tu servicio de {servicio} vence manana ({vencimiento}). Renueva hoy.",
            'mensaje_alerta_hoy' => "Hola {cliente}, tu servicio de {servicio} vence hoy. Renueva para no perder el acceso.",
        ]);
    }
}
