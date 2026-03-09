<?php

function generarMensajeWhatsApp(string $plantilla, array $variables): string {
    $mensaje = $plantilla;
    foreach ($variables as $key => $value) {
        $mensaje = str_replace('{' . $key . '}', $value, $mensaje);
    }
    return $mensaje;
}

function whatsappUrl(string $telefono, string $mensaje): string {
    $telefono = preg_replace('/[^0-9]/', '', $telefono);
    return 'https://wa.me/' . $telefono . '?text=' . rawurlencode($mensaje);
}
