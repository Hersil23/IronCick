<?php

class Tenant {
    private static ?array $panel = null;

    public static function detect(PDO $db): ?array {
        $host = $_SERVER['HTTP_HOST'];
        $parts = explode('.', $host);

        // Desarrollo local: detectar por query param ?panel=subdominio
        if (self::isLocal()) {
            $subdominio = $_GET['panel'] ?? $_SESSION['dev_panel'] ?? null;
            if ($subdominio) {
                $_SESSION['dev_panel'] = $subdominio;
            } else {
                return null;
            }
        } else {
            if (count($parts) !== 3) return null;
            $subdominio = strtolower($parts[0]);
            if ($subdominio === 'app') return null;
        }

        $stmt = $db->prepare(
            "SELECT id, nombre, subdominio, plan, estado, trial_expira, suscripcion_expira, creditos
             FROM paneles WHERE subdominio = ? AND deleted_at IS NULL LIMIT 1"
        );
        $stmt->execute([$subdominio]);
        self::$panel = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if (!self::$panel) return null;

        if (self::$panel['estado'] === 'suspendido') {
            include __DIR__ . '/../views/errors/panel-suspendido.php';
            exit;
        }

        $vencido = false;
        if (!empty(self::$panel['suscripcion_expira'])) {
            $vencido = strtotime(self::$panel['suscripcion_expira']) < time();
        }
        if (!empty(self::$panel['trial_expira']) && empty(self::$panel['suscripcion_expira'])) {
            $vencido = strtotime(self::$panel['trial_expira']) < time();
        }

        if ($vencido) {
            include __DIR__ . '/../views/errors/suscripcion-vencida.php';
            exit;
        }

        return self::$panel;
    }

    public static function get(): ?array {
        return self::$panel;
    }

    public static function id(): int {
        return (int) (self::$panel['id'] ?? 0);
    }

    public static function isLocal(): bool {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        return str_contains($host, 'localhost') || str_contains($host, '127.0.0.1');
    }

    public static function clearDevPanel(): void {
        unset($_SESSION['dev_panel']);
    }

    public static function isSuperAdmin(): bool {
        // En local: super admin si no hay ?panel= en query string
        if (self::isLocal()) {
            // Si explicitamente NO tiene ?panel= en la URL actual, es super admin
            return !isset($_GET['panel']) && empty($_SESSION['dev_panel']);
        }
        $host = $_SERVER['HTTP_HOST'];
        $parts = explode('.', $host);
        return count($parts) === 3 && strtolower($parts[0]) === 'app';
    }
}
