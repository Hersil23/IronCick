-- IronClick Database Schema
-- MySQL / MariaDB
-- Charset: utf8mb4

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- PANELES (clientes del SaaS)
-- =============================================
CREATE TABLE IF NOT EXISTS `paneles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `nombre_contacto` VARCHAR(100) NULL,
    `apellido_contacto` VARCHAR(100) NULL,
    `telefono` VARCHAR(30) NULL,
    `subdominio` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `plan` ENUM('estandar', 'vip') NOT NULL DEFAULT 'estandar',
    `estado` ENUM('activo', 'suspendido', 'eliminado') NOT NULL DEFAULT 'activo',
    `trial_expira` DATETIME NULL,
    `suscripcion_expira` DATETIME NULL,
    `creditos` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `configuracion_json` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    INDEX `idx_subdominio` (`subdominio`),
    INDEX `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- USUARIOS (distribuidores y vendedores dentro de un panel)
-- =============================================
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `panel_id` INT UNSIGNED NOT NULL,
    `rol` ENUM('distribuidor', 'vendedor') NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `telefono` VARCHAR(30) NULL,
    `padre_id` INT UNSIGNED NULL,
    `creditos` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `estado` ENUM('activo', 'suspendido') NOT NULL DEFAULT 'activo',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`panel_id`) REFERENCES `paneles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`padre_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    UNIQUE KEY `uk_email_panel` (`email`, `panel_id`),
    INDEX `idx_panel_rol` (`panel_id`, `rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- CLIENTES FINALES
-- =============================================
CREATE TABLE IF NOT EXISTS `clientes` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `panel_id` INT UNSIGNED NOT NULL,
    `vendedor_id` INT UNSIGNED NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `apellido` VARCHAR(100) NULL DEFAULT '',
    `email` VARCHAR(255) NULL,
    `password` VARCHAR(255) NULL,
    `telefono` VARCHAR(30) NULL,
    `creditos` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `estado` ENUM('activo', 'vencido', 'suspendido', 'eliminado') NOT NULL DEFAULT 'activo',
    `ultimo_pago` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    FOREIGN KEY (`panel_id`) REFERENCES `paneles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    INDEX `idx_panel_estado` (`panel_id`, `estado`),
    INDEX `idx_telefono` (`telefono`),
    INDEX `idx_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- PROVEEDORES
-- =============================================
CREATE TABLE IF NOT EXISTS `proveedores` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `panel_id` INT UNSIGNED NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `contacto` VARCHAR(100) NULL DEFAULT '',
    `telefono` VARCHAR(30) NULL DEFAULT '',
    `notas` TEXT NULL,
    `estado` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    FOREIGN KEY (`panel_id`) REFERENCES `paneles`(`id`) ON DELETE CASCADE,
    INDEX `idx_panel` (`panel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- SERVICIOS
-- =============================================
CREATE TABLE IF NOT EXISTS `servicios` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `panel_id` INT UNSIGNED NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `precio_usd` DECIMAL(10,2) NOT NULL,
    `duracion_dias` INT NOT NULL DEFAULT 30,
    `perfiles_por_cuenta` INT NOT NULL DEFAULT 1,
    `descripcion` TEXT NULL,
    `imagen` VARCHAR(255) NULL,
    `imap_correo` VARCHAR(255) NULL,
    `imap_password_enc` TEXT NULL,
    `estado` ENUM('activo', 'inactivo') NOT NULL DEFAULT 'activo',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`panel_id`) REFERENCES `paneles`(`id`) ON DELETE CASCADE,
    INDEX `idx_panel_estado` (`panel_id`, `estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- CUENTAS MAESTRAS
-- =============================================
CREATE TABLE IF NOT EXISTS `cuentas` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `panel_id` INT UNSIGNED NOT NULL,
    `servicio_id` INT UNSIGNED NOT NULL,
    `proveedor_id` INT UNSIGNED NULL,
    `correo` VARCHAR(255) NOT NULL,
    `password_enc` TEXT NOT NULL,
    `costo_usd` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `fecha_vencimiento` DATE NOT NULL,
    `estado` ENUM('activa', 'vencida', 'suspendida') NOT NULL DEFAULT 'activa',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`panel_id`) REFERENCES `paneles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`servicio_id`) REFERENCES `servicios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores`(`id`) ON DELETE SET NULL,
    INDEX `idx_panel_servicio` (`panel_id`, `servicio_id`),
    INDEX `idx_vencimiento` (`fecha_vencimiento`),
    INDEX `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- PERFILES
-- =============================================
CREATE TABLE IF NOT EXISTS `perfiles` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cuenta_id` INT UNSIGNED NOT NULL,
    `numero_perfil` INT NOT NULL DEFAULT 1,
    `pin` VARCHAR(20) NULL DEFAULT '',
    `estado` ENUM('disponible', 'vendido') NOT NULL DEFAULT 'disponible',
    FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas`(`id`) ON DELETE CASCADE,
    INDEX `idx_cuenta_estado` (`cuenta_id`, `estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- VENTAS
-- =============================================
CREATE TABLE IF NOT EXISTS `ventas` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `panel_id` INT UNSIGNED NOT NULL,
    `cliente_id` INT UNSIGNED NOT NULL,
    `vendedor_id` INT UNSIGNED NULL,
    `servicio_id` INT UNSIGNED NOT NULL,
    `cuenta_id` INT UNSIGNED NOT NULL,
    `perfil_id` INT UNSIGNED NOT NULL,
    `numero_orden` VARCHAR(20) NOT NULL UNIQUE,
    `precio_usd` DECIMAL(10,2) NOT NULL,
    `precio_local` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `fecha_compra` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `fecha_vencimiento` DATE NOT NULL,
    `tipo` ENUM('nueva', 'renovacion') NOT NULL DEFAULT 'nueva',
    `estado` ENUM('activa', 'vencida', 'suspendida') NOT NULL DEFAULT 'activa',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`panel_id`) REFERENCES `paneles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`servicio_id`) REFERENCES `servicios`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`perfil_id`) REFERENCES `perfiles`(`id`) ON DELETE CASCADE,
    INDEX `idx_panel_estado` (`panel_id`, `estado`),
    INDEX `idx_cliente` (`cliente_id`),
    INDEX `idx_vencimiento` (`fecha_vencimiento`),
    INDEX `idx_orden` (`numero_orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MOVIMIENTOS DE CREDITOS
-- =============================================
CREATE TABLE IF NOT EXISTS `movimientos_creditos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `panel_id` INT UNSIGNED NOT NULL,
    `tipo` ENUM('entrada', 'salida') NOT NULL,
    `origen` VARCHAR(50) NOT NULL,
    `destino_tipo` VARCHAR(50) NOT NULL,
    `destino_id` INT UNSIGNED NOT NULL,
    `monto` DECIMAL(12,2) NOT NULL,
    `concepto` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`panel_id`) REFERENCES `paneles`(`id`) ON DELETE CASCADE,
    INDEX `idx_panel` (`panel_id`),
    INDEX `idx_fecha` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- PAGOS (confirmaciones)
-- =============================================
CREATE TABLE IF NOT EXISTS `pagos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `panel_id` INT UNSIGNED NOT NULL,
    `cliente_id` INT UNSIGNED NOT NULL,
    `venta_id` INT UNSIGNED NULL,
    `monto_usd` DECIMAL(10,2) NOT NULL,
    `monto_local` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `tasa_cambio` DECIMAL(15,4) NOT NULL DEFAULT 1.0000,
    `metodo` VARCHAR(50) NULL DEFAULT '',
    `comprobante_url` VARCHAR(255) NULL,
    `estado` ENUM('pendiente', 'confirmado') NOT NULL DEFAULT 'pendiente',
    `confirmado_por` INT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`panel_id`) REFERENCES `paneles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`venta_id`) REFERENCES `ventas`(`id`) ON DELETE SET NULL,
    INDEX `idx_panel_estado` (`panel_id`, `estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- HISTORIAL IMAP
-- =============================================
CREATE TABLE IF NOT EXISTS `historial_imap` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `panel_id` INT UNSIGNED NOT NULL,
    `servicio_id` INT UNSIGNED NULL,
    `cliente_id` INT UNSIGNED NULL,
    `codigo` VARCHAR(20) NOT NULL,
    `enviado_whatsapp` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`panel_id`) REFERENCES `paneles`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`servicio_id`) REFERENCES `servicios`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE SET NULL,
    INDEX `idx_panel` (`panel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- CONFIGURACION DEL PANEL
-- =============================================
CREATE TABLE IF NOT EXISTS `configuracion` (
    `panel_id` INT UNSIGNED PRIMARY KEY,
    `moneda` VARCHAR(5) NOT NULL DEFAULT 'USD',
    `tasa_fuente` VARCHAR(20) NOT NULL DEFAULT 'binance',
    `tasa_manual` DECIMAL(15,4) NULL,
    `margen_recargo` DECIMAL(5,2) NULL DEFAULT 0.00,
    `banco` VARCHAR(100) NULL DEFAULT '',
    `telefono_pago` VARCHAR(30) NULL DEFAULT '',
    `cuenta_banco` VARCHAR(50) NULL DEFAULT '',
    `dias_suspension` INT NOT NULL DEFAULT 90,
    `whatsapp_contacto` VARCHAR(30) NULL DEFAULT '',
    `mensaje_entrega` TEXT NULL,
    `mensaje_cambio` TEXT NULL,
    `mensaje_cobro` TEXT NULL,
    `mensaje_cobro_grupal` TEXT NULL,
    `mensaje_renovacion` TEXT NULL,
    `mensaje_renovacion_grupal` TEXT NULL,
    `mensaje_alerta_3dias` TEXT NULL,
    `mensaje_alerta_1dia` TEXT NULL,
    `mensaje_alerta_hoy` TEXT NULL,
    `mensaje_bienvenida` TEXT NULL,
    `mensaje_recarga` TEXT NULL,
    `mensaje_imap` TEXT NULL,
    `tienda_colores_json` TEXT NULL,
    `tienda_logo` VARCHAR(255) NULL,
    `tienda_descripcion` TEXT NULL,
    `tienda_bienvenida` TEXT NULL,
    `tienda_faq_json` TEXT NULL,
    `tienda_redes_json` TEXT NULL,
    FOREIGN KEY (`panel_id`) REFERENCES `paneles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
