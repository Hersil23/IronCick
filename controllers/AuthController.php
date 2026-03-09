<?php

require_once __DIR__ . '/../models/Panel.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Configuracion.php';

class AuthController {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function login(?array $panelContext): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (!$email || !$password) {
            Response::error('Email y contrasena son requeridos.');
        }

        $this->checkRateLimit($email);

        // Super Admin login (app.ironclick.app)
        if (Tenant::isSuperAdmin()) {
            $panelModel = new Panel($this->db);
            // Super admin stored as first panel with special flag or separate table
            // For simplicity, check if email matches env super admin
            if ($email === ($_ENV['SUPER_ADMIN_EMAIL'] ?? '')) {
                if (password_verify($password, $_ENV['SUPER_ADMIN_PASS_HASH'] ?? '')) {
                    $_SESSION['usuario_id'] = 0;
                    $_SESSION['panel_id'] = 0;
                    $_SESSION['rol'] = 'super_admin';
                    $_SESSION['nombre'] = 'Super Admin';
                    $_SESSION['email'] = $email;
                    session_regenerate_id(true);
                    $this->resetRateLimit($email);
                    Response::success(['redirect' => '/dashboard']);
                }
            }
            Response::error('Credenciales invalidas.', 401);
        }

        // Panel login or user login within panel
        if ($panelContext) {
            $panelId = (int) $panelContext['id'];

            // Try panel owner login
            $panelModel = new Panel($this->db);
            $panel = $panelModel->findByEmail($email);
            if ($panel && (int)$panel['id'] === $panelId && password_verify($password, $panel['password'])) {
                $_SESSION['usuario_id'] = $panel['id'];
                $_SESSION['panel_id'] = $panel['id'];
                $_SESSION['rol'] = 'panel';
                $_SESSION['nombre'] = $panel['nombre'];
                $_SESSION['email'] = $email;
                session_regenerate_id(true);
                $this->resetRateLimit($email);
                Response::success(['redirect' => '/dashboard']);
            }

            // Try distribuidor/vendedor login
            $usuarioModel = new Usuario($this->db);
            $usuario = $usuarioModel->findByEmail($email, $panelId);
            if ($usuario && password_verify($password, $usuario['password'])) {
                if ($usuario['estado'] !== 'activo') {
                    Response::error('Tu cuenta esta suspendida.', 403);
                }
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['panel_id'] = $panelId;
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['email'] = $email;
                $_SESSION['padre_id'] = $usuario['padre_id'];
                session_regenerate_id(true);
                $this->resetRateLimit($email);
                Response::success(['redirect' => '/dashboard']);
            }

            // Try client login
            $stmt = $this->db->prepare("SELECT * FROM clientes WHERE email = ? AND panel_id = ? AND deleted_at IS NULL");
            $stmt->execute([$email, $panelId]);
            $cliente = $stmt->fetch();
            if ($cliente && password_verify($password, $cliente['password'] ?? '')) {
                $_SESSION['usuario_id'] = $cliente['id'];
                $_SESSION['panel_id'] = $panelId;
                $_SESSION['rol'] = 'cliente';
                $_SESSION['nombre'] = $cliente['nombre'];
                $_SESSION['email'] = $email;
                session_regenerate_id(true);
                $this->resetRateLimit($email);
                Response::success(['redirect' => '/mi-cuenta']);
            }
        }

        Response::error('Credenciales invalidas.', 401);
    }

    public function registro(): void {
        $input = json_decode(file_get_contents('php://input'), true);
        $nombreContacto = trim($input['nombre_contacto'] ?? '');
        $apellidoContacto = trim($input['apellido_contacto'] ?? '');
        $telefono = trim($input['telefono'] ?? '');
        $nombre = trim($input['nombre'] ?? '');
        $subdominio = strtolower(trim($input['subdominio'] ?? ''));
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $plan = in_array($input['plan'] ?? '', ['estandar', 'vip']) ? $input['plan'] : 'estandar';

        if (!$nombreContacto || !$apellidoContacto || !$telefono || !$nombre || !$subdominio || !$email || !$password) {
            Response::error('Todos los campos son requeridos.');
        }

        if (strlen($password) < 6) {
            Response::error('La contrasena debe tener al menos 6 caracteres.');
        }

        if (!preg_match('/^[a-z0-9\-]{3,30}$/', $subdominio)) {
            Response::error('El subdominio solo puede contener letras minusculas, numeros y guiones (3-30 caracteres).');
        }

        $reserved = ['app', 'www', 'api', 'admin', 'mail', 'ftp', 'test', 'demo', 'staging'];
        if (in_array($subdominio, $reserved)) {
            Response::error('Este subdominio esta reservado.');
        }

        $panelModel = new Panel($this->db);

        if ($panelModel->findBySubdominio($subdominio)) {
            Response::error('Este subdominio ya esta en uso.');
        }

        if ($panelModel->findByEmail($email)) {
            Response::error('Este email ya esta registrado.');
        }

        $trialExpira = date('Y-m-d H:i:s', strtotime('+' . TRIAL_DAYS . ' days'));

        $panelId = $panelModel->create([
            'nombre' => $nombre,
            'nombre_contacto' => $nombreContacto,
            'apellido_contacto' => $apellidoContacto,
            'telefono' => $telefono,
            'subdominio' => $subdominio,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]),
            'plan' => $plan,
            'estado' => 'activo',
            'trial_expira' => $trialExpira,
            'creditos' => 0,
        ]);

        // Auto login
        $_SESSION['usuario_id'] = $panelId;
        $_SESSION['panel_id'] = $panelId;
        $_SESSION['rol'] = 'panel';
        $_SESSION['nombre'] = $nombre;
        $_SESSION['email'] = $email;
        session_regenerate_id(true);

        // Init default config
        $configModel = new Configuracion($this->db);
        $configModel->get($panelId);

        $redirect = Tenant::isLocal()
            ? '/dashboard?panel=' . $subdominio
            : '/dashboard';

        Response::success(['redirect' => $redirect], 'Panel creado exitosamente.');
    }

    public function checkSubdominio(): void {
        $subdominio = strtolower(trim($_GET['subdominio'] ?? ''));
        if (!$subdominio || !preg_match('/^[a-z0-9\-]{3,30}$/', $subdominio)) {
            Response::success(['available' => false]);
        }

        $reserved = ['app', 'www', 'api', 'admin', 'mail', 'ftp', 'test', 'demo', 'staging'];
        if (in_array($subdominio, $reserved)) {
            Response::success(['available' => false]);
        }

        $panelModel = new Panel($this->db);
        $exists = $panelModel->findBySubdominio($subdominio);
        Response::success(['available' => !$exists]);
    }

    public function logout(): void {
        session_destroy();
        Response::success(null, 'Sesion cerrada.');
    }

    private function checkRateLimit(string $email): void {
        $key = 'login_attempts_' . md5($email . $_SERVER['REMOTE_ADDR']);

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'first_attempt' => time()];
        }

        $data = &$_SESSION[$key];

        if (time() - $data['first_attempt'] > RATE_LIMIT_WINDOW) {
            $data = ['count' => 0, 'first_attempt' => time()];
        }

        if ($data['count'] >= RATE_LIMIT_ATTEMPTS) {
            $espera = RATE_LIMIT_WINDOW - (time() - $data['first_attempt']);
            Response::error("Demasiados intentos. Espera {$espera} segundos.", 429);
        }

        $data['count']++;
    }

    private function resetRateLimit(string $email): void {
        $key = 'login_attempts_' . md5($email . $_SERVER['REMOTE_ADDR']);
        unset($_SESSION[$key]);
    }
}
