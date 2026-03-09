<?php

function obtenerTasa(string $tipo): void {
    $data = null;

    switch ($tipo) {
        case 'paralelo':
            $json = @file_get_contents('https://pydolarvenezuela-api.vercel.app/api/v1/dollar?page=enparalelovzla');
            if ($json) {
                $resp = json_decode($json, true);
                $data = ['tasa' => (float) ($resp['monitors']['usd']['price'] ?? 0), 'fuente' => 'EnParaleloVzla'];
            }
            break;

        case 'bcv':
            $json = @file_get_contents('https://pydolarvenezuela-api.vercel.app/api/v1/dollar?page=bcv');
            if ($json) {
                $resp = json_decode($json, true);
                $data = ['tasa' => (float) ($resp['monitors']['usd']['price'] ?? 0), 'fuente' => 'BCV'];
            }
            break;

        case 'binance':
            $json = @file_get_contents('https://api.binance.com/api/v3/ticker/price?symbol=USDTVES');
            if ($json) {
                $resp = json_decode($json, true);
                $data = ['tasa' => (float) ($resp['price'] ?? 0), 'fuente' => 'Binance USDT/VES'];
            }
            if (!$data || !$data['tasa']) {
                $json = @file_get_contents('https://pydolarvenezuela-api.vercel.app/api/v1/dollar?page=binance');
                if ($json) {
                    $resp = json_decode($json, true);
                    $data = ['tasa' => (float) ($resp['monitors']['usd']['price'] ?? 0), 'fuente' => 'Binance (alt)'];
                }
            }
            break;

        default:
            Response::error('Tipo de tasa no valido. Usa: paralelo, bcv, binance');
    }

    if (!$data || !$data['tasa']) {
        Response::error('No se pudo obtener la tasa. Intenta mas tarde.');
    }

    $data['timestamp'] = date('Y-m-d H:i:s');
    Response::success($data);
}
