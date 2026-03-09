<?php

class Auth {
    const ROLES = ['super_admin', 'panel', 'distribuidor', 'vendedor', 'cliente'];

    public static function check(string $rolRequerido = null): void {
        if (!isset($_SESSION['usuario_id'])) {
            if (self::isAjax()) {
                Response::error('No autenticado.', 401);
            }
            header('Location: /login');
            exit;
        }

        if ($rolRequerido && !self::canAccess($rolRequerido)) {
            if (self::isAjax()) {
                Response::error('Sin permisos.', 403);
            }
            http_response_code(403);
            include __DIR__ . '/../views/errors/403.php';
            exit;
        }
    }

    public static function canAccess(string $rolRequerido): bool {
        $jerarquia = array_flip(self::ROLES);
        $rolActual = $_SESSION['rol'] ?? '';
        return isset($jerarquia[$rolActual]) &&
               isset($jerarquia[$rolRequerido]) &&
               $jerarquia[$rolActual] <= $jerarquia[$rolRequerido];
    }

    public static function rol(): string {
        return $_SESSION['rol'] ?? '';
    }

    public static function panelId(): int {
        return (int) ($_SESSION['panel_id'] ?? 0);
    }

    public static function usuarioId(): int {
        return (int) ($_SESSION['usuario_id'] ?? 0);
    }

    public static function nombre(): string {
        return $_SESSION['nombre'] ?? '';
    }

    public static function isSuperAdmin(): bool {
        return self::rol() === 'super_admin';
    }

    public static function isPanel(): bool {
        return self::rol() === 'panel';
    }

    public static function isDistribuidor(): bool {
        return self::rol() === 'distribuidor';
    }

    public static function isVendedor(): bool {
        return self::rol() === 'vendedor';
    }

    public static function isCliente(): bool {
        return self::rol() === 'cliente';
    }

    private static function isAjax(): bool {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
