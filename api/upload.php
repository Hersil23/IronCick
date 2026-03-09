<?php

require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../middleware/csrf.php';

Auth::check();
CSRF::check();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Metodo no permitido.', 405);
}

if (empty($_FILES['file'])) {
    Response::error('No se recibio archivo.');
}

try {
    $nombre = Upload::imagen($_FILES['file'], __DIR__ . '/../uploads');
    Response::success(['filename' => $nombre, 'url' => '/uploads/' . $nombre]);
} catch (Exception $e) {
    Response::error($e->getMessage());
}
