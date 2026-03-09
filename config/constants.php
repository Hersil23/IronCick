<?php

define('APP_NAME', 'IronClick');
define('APP_DOMAIN', 'ironclick.app');
define('APP_VERSION', '1.0.0');

define('ITEMS_PER_PAGE', 25);

define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024);
define('UPLOAD_ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('UPLOAD_ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

define('BCRYPT_COST', 12);
define('RATE_LIMIT_ATTEMPTS', 5);
define('RATE_LIMIT_WINDOW', 900);

define('TRIAL_DAYS', 3);
define('DEFAULT_SUSPENSION_DAYS', 90);

define('ROLES', ['super_admin', 'panel', 'distribuidor', 'vendedor', 'cliente']);

define('MONEDAS', [
    'USD' => 'Dolar Estadounidense',
    'VES' => 'Bolivar Venezolano',
    'COP' => 'Peso Colombiano',
    'MXN' => 'Peso Mexicano',
    'ARS' => 'Peso Argentino',
    'PEN' => 'Sol Peruano',
    'CLP' => 'Peso Chileno',
    'BRL' => 'Real Brasileno',
    'DOP' => 'Peso Dominicano',
    'EUR' => 'Euro',
]);

define('TASA_FUENTES', ['paralelo', 'bcv', 'binance', 'manual']);
