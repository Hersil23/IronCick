<?php

require_once __DIR__ . '/config/env.php';
loadEnv(__DIR__ . '/.env');

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/middleware/csrf.php';
require_once __DIR__ . '/middleware/auth.php';
require_once __DIR__ . '/middleware/tenant.php';

Crypto::init();

$db = Database::get();
$url = trim($_GET['url'] ?? '', '/');
$method = $_SERVER['REQUEST_METHOD'];

// Actualizar ventas vencidas
require_once __DIR__ . '/models/Venta.php';
$ventaModel = new Venta($db);
$ventaModel->actualizarVencidas();

// Detectar contexto
$isSuperAdmin = Tenant::isSuperAdmin();
$panel = null;

if (!$isSuperAdmin) {
    $panel = Tenant::detect($db);
}

// Archivos estaticos
if (preg_match('/^assets\//', $url) || preg_match('/^uploads\//', $url)) {
    return false;
}

// --- RUTAS API ---
if (str_starts_with($url, 'api/')) {
    header('Content-Type: application/json');

    // CSRF en POST/PUT/DELETE
    if (in_array($method, ['POST', 'PUT', 'DELETE']) && $url !== 'api/auth/login') {
        CSRF::check();
    }

    $apiRoute = substr($url, 4);

    // Auth
    if ($apiRoute === 'auth/login' && $method === 'POST') {
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController($db))->login($panel);
        exit;
    }
    if ($apiRoute === 'auth/logout' && $method === 'POST') {
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController($db))->logout();
        exit;
    }
    if ($apiRoute === 'auth/registro' && $method === 'POST') {
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController($db))->registro();
        exit;
    }
    if ($apiRoute === 'auth/check-subdominio' && $method === 'GET') {
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController($db))->checkSubdominio();
        exit;
    }

    // Requiere autenticacion desde aqui
    Auth::check();
    $panelId = Auth::panelId();

    // Dashboard
    if (str_starts_with($apiRoute, 'dashboard/')) {
        require_once __DIR__ . '/controllers/DashboardController.php';
        $ctrl = new DashboardController($db);
        match ($apiRoute) {
            'dashboard/metricas' => $ctrl->metricas($panelId),
            'dashboard/stock' => $ctrl->stock($panelId),
            'dashboard/ventas-cobrar' => $ctrl->ventasCobrar($panelId),
            'dashboard/cuentas-vencer' => $ctrl->cuentasVencer($panelId),
            'dashboard/ultimos-pagos' => $ctrl->ultimosPagos($panelId),
            default => Response::error('Ruta no encontrada.', 404),
        };
        exit;
    }

    // Servicios
    if (str_starts_with($apiRoute, 'servicios')) {
        require_once __DIR__ . '/controllers/ServiciosController.php';
        $ctrl = new ServiciosController($db);
        if ($apiRoute === 'servicios' && $method === 'GET') { $ctrl->index($panelId); exit; }
        if ($apiRoute === 'servicios' && $method === 'POST') { $ctrl->create($panelId); exit; }
        if (preg_match('/^servicios\/(\d+)$/', $apiRoute, $m)) {
            if ($method === 'PUT') { $ctrl->update((int)$m[1], $panelId); exit; }
            if ($method === 'DELETE') { $ctrl->delete((int)$m[1], $panelId); exit; }
        }
    }

    // Cuentas
    if (str_starts_with($apiRoute, 'cuentas')) {
        require_once __DIR__ . '/controllers/CuentasController.php';
        $ctrl = new CuentasController($db);
        if ($apiRoute === 'cuentas' && $method === 'GET') { $ctrl->index($panelId); exit; }
        if ($apiRoute === 'cuentas' && $method === 'POST') { $ctrl->create($panelId); exit; }
        if (preg_match('/^cuentas\/(\d+)$/', $apiRoute, $m)) {
            if ($method === 'PUT') { $ctrl->update((int)$m[1], $panelId); exit; }
            if ($method === 'DELETE') { $ctrl->delete((int)$m[1], $panelId); exit; }
        }
        if (preg_match('/^cuentas\/(\d+)\/perfiles$/', $apiRoute, $m)) {
            $ctrl->perfiles((int)$m[1], $panelId); exit;
        }
    }

    // Proveedores
    if (str_starts_with($apiRoute, 'proveedores')) {
        require_once __DIR__ . '/controllers/ProveedoresController.php';
        $ctrl = new ProveedoresController($db);
        if ($apiRoute === 'proveedores' && $method === 'GET') { $ctrl->index($panelId); exit; }
        if ($apiRoute === 'proveedores' && $method === 'POST') { $ctrl->create($panelId); exit; }
        if (preg_match('/^proveedores\/(\d+)$/', $apiRoute, $m) && $method === 'PUT') {
            $ctrl->update((int)$m[1], $panelId); exit;
        }
    }

    // Clientes
    if (str_starts_with($apiRoute, 'clientes')) {
        require_once __DIR__ . '/controllers/ClientesController.php';
        $ctrl = new ClientesController($db);
        if ($apiRoute === 'clientes' && $method === 'GET') { $ctrl->index($panelId); exit; }
        if ($apiRoute === 'clientes' && $method === 'POST') { $ctrl->create($panelId); exit; }
        if (preg_match('/^clientes\/(\d+)$/', $apiRoute, $m) && $method === 'PUT') {
            $ctrl->update((int)$m[1], $panelId); exit;
        }
        if (preg_match('/^clientes\/(\d+)\/estado$/', $apiRoute, $m) && $method === 'PUT') {
            $ctrl->updateEstado((int)$m[1], $panelId); exit;
        }
        if (preg_match('/^clientes\/(\d+)\/ventas$/', $apiRoute, $m) && $method === 'GET') {
            $ctrl->ventas((int)$m[1], $panelId); exit;
        }
    }

    // Ventas
    if (str_starts_with($apiRoute, 'ventas')) {
        require_once __DIR__ . '/controllers/VentasController.php';
        $ctrl = new VentasController($db);
        if ($apiRoute === 'ventas' && $method === 'GET') { $ctrl->index($panelId); exit; }
        if ($apiRoute === 'ventas' && $method === 'POST') { $ctrl->create($panelId); exit; }
        if (preg_match('/^ventas\/(\d+)\/renovar$/', $apiRoute, $m) && $method === 'PUT') {
            $ctrl->renovar((int)$m[1], $panelId); exit;
        }
        if (preg_match('/^ventas\/(\d+)\/pago$/', $apiRoute, $m) && $method === 'PUT') {
            $ctrl->marcarPago((int)$m[1], $panelId); exit;
        }
        if ($apiRoute === 'ventas/cobro-grupal' && $method === 'POST') { $ctrl->cobroGrupal($panelId); exit; }
        if ($apiRoute === 'ventas/renovacion-grupal' && $method === 'POST') { $ctrl->renovacionGrupal($panelId); exit; }
        if (preg_match('/^ventas\/(\d+)$/', $apiRoute, $m) && $method === 'DELETE') {
            $ctrl->delete((int)$m[1], $panelId); exit;
        }
    }

    // Creditos
    if (str_starts_with($apiRoute, 'creditos')) {
        require_once __DIR__ . '/controllers/CreditosController.php';
        $ctrl = new CreditosController($db);
        if ($apiRoute === 'creditos/movimientos') { $ctrl->movimientos($panelId); exit; }
        if ($apiRoute === 'creditos/vender' && $method === 'POST') { $ctrl->venderAPanel(); exit; }
        if ($apiRoute === 'creditos/generar' && $method === 'POST') { $ctrl->generar($panelId); exit; }
        if ($apiRoute === 'creditos/acreditar' && $method === 'POST') { $ctrl->acreditar($panelId); exit; }
    }

    // IMAP
    if (str_starts_with($apiRoute, 'imap')) {
        require_once __DIR__ . '/controllers/ImapController.php';
        $ctrl = new ImapController($db);
        if ($apiRoute === 'imap/buscar-codigo' && $method === 'POST') { $ctrl->buscarCodigo($panelId); exit; }
        if ($apiRoute === 'imap/historial') { $ctrl->historial($panelId); exit; }
    }

    // Tasas
    if (str_starts_with($apiRoute, 'tasa/')) {
        require_once __DIR__ . '/api/tasa.php';
        $tipo = substr($apiRoute, 5);
        obtenerTasa($tipo);
        exit;
    }

    // Reportes
    if (str_starts_with($apiRoute, 'reportes')) {
        require_once __DIR__ . '/controllers/ReportesController.php';
        $ctrl = new ReportesController($db);
        match ($apiRoute) {
            'reportes/ventas' => $ctrl->ventas($panelId),
            'reportes/vendedores' => $ctrl->vendedores($panelId),
            'reportes/servicios' => $ctrl->servicios($panelId),
            'reportes/clientes' => $ctrl->clientes($panelId),
            default => Response::error('Ruta no encontrada.', 404),
        };
        exit;
    }

    // Configuracion
    if (str_starts_with($apiRoute, 'configuracion')) {
        require_once __DIR__ . '/controllers/ConfiguracionController.php';
        $ctrl = new ConfiguracionController($db);
        if ($apiRoute === 'configuracion' && $method === 'GET') { $ctrl->get($panelId); exit; }
        if ($apiRoute === 'configuracion/guardar' && $method === 'POST') { $ctrl->save($panelId); exit; }
        if ($apiRoute === 'configuracion/upload-logo' && $method === 'POST') { $ctrl->uploadLogo($panelId); exit; }
    }

    // Lista de precios
    if ($apiRoute === 'lista-precios') {
        require_once __DIR__ . '/controllers/ServiciosController.php';
        (new ServiciosController($db))->listaPrecios($panelId);
        exit;
    }

    Response::error('Ruta API no encontrada.', 404);
    exit;
}

// --- RUTAS DE VISTAS ---

// Tienda publica
if (!$isSuperAdmin && $panel) {
    require_once __DIR__ . '/controllers/TiendaController.php';
    $tiendaCtrl = new TiendaController($db);

    $tiendaRoutes = ['tienda', 'catalogo', 'como-comprar', 'faq'];
    if (in_array($url, $tiendaRoutes) || $url === '' || str_starts_with($url, 'pedido/') || $url === 'mi-cuenta') {
        // Rutas publicas de tienda no requieren auth para ver
        if ($url === '' || $url === 'tienda') { $tiendaCtrl->landing($panel); exit; }
        if ($url === 'catalogo') { $tiendaCtrl->catalogo($panel); exit; }
        if ($url === 'como-comprar') { $tiendaCtrl->comoComprar($panel); exit; }
        if ($url === 'faq') { $tiendaCtrl->faq($panel); exit; }
        if (preg_match('/^pedido\/(.+)$/', $url, $m)) { $tiendaCtrl->pedido($panel, $m[1]); exit; }
        if ($url === 'mi-cuenta') { $tiendaCtrl->miCuenta($panel); exit; }
    }
}

// Landing page principal (Super Admin / dominio principal)
if ($url === '' && $isSuperAdmin && empty($_SESSION['rol'])) {
    include __DIR__ . '/views/landing/index.php';
    exit;
}

// Registro
if ($url === 'registro') {
    include __DIR__ . '/views/auth/registro.php';
    exit;
}

// Login
if ($url === 'login') {
    // En local: si accede a /login sin ?panel=, limpiar contexto de panel
    if (Tenant::isLocal() && !isset($_GET['panel'])) {
        Tenant::clearDevPanel();
    }
    include __DIR__ . '/views/auth/login.php';
    exit;
}

// Logout
if ($url === 'logout') {
    session_destroy();
    header('Location: /login');
    exit;
}

// Dashboard y modulos (requieren autenticacion)
Auth::check();

$viewMap = [
    'dashboard'     => 'views/dashboard/index.php',
    'servicios'     => 'views/servicios/index.php',
    'cuentas'       => 'views/cuentas/index.php',
    'proveedores'   => 'views/proveedores/index.php',
    'clientes'      => 'views/clientes/index.php',
    'ventas'        => 'views/ventas/index.php',
    'reportes'      => 'views/reportes/index.php',
    'configuracion' => 'views/configuracion/index.php',
    'vendedores'    => 'views/ventas/vendedores.php',
    'distribuidores'=> 'views/ventas/distribuidores.php',
    'creditos'      => 'views/dashboard/creditos.php',
];

if ($url === '' || $url === 'dashboard') {
    $url = 'dashboard';
}

if (isset($viewMap[$url])) {
    $pageTitle = ucfirst($url) . ' - ' . APP_NAME;
    $currentPage = $url;
    include __DIR__ . '/views/layouts/admin.php';
    exit;
}

// 404
http_response_code(404);
include __DIR__ . '/views/errors/404.php';
