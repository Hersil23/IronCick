<?php

class Response {
    public static function success(mixed $data = null, string $msg = 'OK', int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => $msg, 'data' => $data]);
        exit;
    }

    public static function error(string $msg, int $code = 400, mixed $data = null): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $msg, 'data' => $data]);
        exit;
    }
}

class Crypto {
    private static string $key;

    public static function init(): void {
        self::$key = base64_decode($_ENV['ENCRYPTION_KEY']);
    }

    public static function encrypt(string $data): string {
        $iv = random_bytes(openssl_cipher_iv_length('aes-256-gcm'));
        $tag = '';
        $encrypted = openssl_encrypt($data, 'aes-256-gcm', self::$key, 0, $iv, $tag);
        return base64_encode($iv . $tag . $encrypted);
    }

    public static function decrypt(string $data): string {
        $raw = base64_decode($data);
        $ivLen = openssl_cipher_iv_length('aes-256-gcm');
        $iv  = substr($raw, 0, $ivLen);
        $tag = substr($raw, $ivLen, 16);
        $enc = substr($raw, $ivLen + 16);
        return openssl_decrypt($enc, 'aes-256-gcm', self::$key, 0, $iv, $tag);
    }
}

class Upload {
    public static function imagen(array $file, string $destino): string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir archivo.');
        }

        if ($file['size'] > UPLOAD_MAX_SIZE) {
            throw new Exception('Archivo demasiado grande. Maximo 2MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoReal = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($tipoReal, UPLOAD_ALLOWED_TYPES)) {
            throw new Exception('Tipo de archivo no permitido.');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, UPLOAD_ALLOWED_EXTENSIONS)) {
            throw new Exception('Extension no permitida.');
        }

        $nombre = bin2hex(random_bytes(16)) . '.' . $ext;
        $ruta   = $destino . '/' . $nombre;

        if (!move_uploaded_file($file['tmp_name'], $ruta)) {
            throw new Exception('No se pudo guardar el archivo.');
        }

        return $nombre;
    }
}

function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function saludo(): string {
    $hora = (int) date('H');
    if ($hora >= 6 && $hora < 12) return 'Buenos dias';
    if ($hora >= 12 && $hora < 19) return 'Buenas tardes';
    return 'Buenas noches';
}

function fechaCompleta(): string {
    $dias = ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'];
    $meses = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $d = $dias[(int)date('w')];
    $m = $meses[(int)date('n')];
    return $d . ', ' . date('d') . ' de ' . $m . ' de ' . date('Y');
}

function generarOrden(): string {
    return 'IC-' . strtoupper(bin2hex(random_bytes(4)));
}

function paginacion(int $total, int $pagina, int $porPagina = ITEMS_PER_PAGE): array {
    $totalPaginas = max(1, (int) ceil($total / $porPagina));
    $pagina = max(1, min($pagina, $totalPaginas));
    $offset = ($pagina - 1) * $porPagina;
    return [
        'total'         => $total,
        'pagina'        => $pagina,
        'por_pagina'    => $porPagina,
        'total_paginas' => $totalPaginas,
        'offset'        => $offset,
    ];
}
