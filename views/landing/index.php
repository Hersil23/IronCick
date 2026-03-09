<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IronClick - Plataforma SaaS para Servicios Digitales</title>

    <meta name="description" content="Administra y vende servicios digitales de streaming, IPTV y software con tu propio panel personalizado. Multi-tenant, creditos, tienda publica y mas.">
    <meta property="og:title" content="IronClick - Plataforma SaaS para Servicios Digitales">
    <meta property="og:description" content="Tu propio panel para gestionar y vender servicios digitales.">
    <meta property="og:image" content="/assets/img/logo/og-image.png">
    <link rel="canonical" href="https://ironclick.app">

    <link rel="icon" type="image/x-icon" href="/assets/img/logo/favicon.ico">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#060608">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                colors: { accent: '#ea580c', 'accent-hover': '#f97316' },
                fontFamily: { syne: ['Syne', 'sans-serif'], dm: ['DM Sans', 'sans-serif'], orbitron: ['Orbitron', 'sans-serif'] }
            }
        }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@600;700;800&family=Orbitron:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <link rel="stylesheet" href="/assets/css/app.css">

    <style>
        .gradient-text { background: linear-gradient(135deg, #ea580c, #f97316, #fb923c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .hero-glow { background: radial-gradient(ellipse at center, rgba(234,88,12,0.15) 0%, transparent 70%); }
        .card-feature { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); transition: all 0.3s ease; }
        .card-feature:hover { background: rgba(255,255,255,0.06); border-color: rgba(234,88,12,0.3); transform: translateY(-4px); }
        .plan-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); }
        .plan-card.popular { border-color: #ea580c; background: rgba(234,88,12,0.05); }
        .plan-card.popular .badge { background: #ea580c; }
        .stat-number { font-variant-numeric: tabular-nums; }
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
        .grid-bg { background-image: radial-gradient(rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 40px 40px; }
    </style>
</head>
<body class="bg-[#060608] text-slate-200 font-dm min-h-screen grid-bg">

    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-[#060608]/80 backdrop-blur-xl border-b border-white/[0.06]">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <img src="/assets/img/logo/logo-icon.svg" alt="IronClick" class="h-9 w-9">
                <span class="text-xl font-orbitron font-bold gradient-text">IronClick</span>
            </a>
            <div class="hidden md:flex items-center gap-8 text-sm">
                <a href="#features" class="text-slate-400 hover:text-white transition-colors">Funciones</a>
                <a href="#modules" class="text-slate-400 hover:text-white transition-colors">Modulos</a>
                <a href="#plans" class="text-slate-400 hover:text-white transition-colors">Planes</a>
                <a href="#faq" class="text-slate-400 hover:text-white transition-colors">FAQ</a>
                <a href="/registro" class="px-5 py-2.5 bg-white/[0.06] hover:bg-white/[0.1] border border-white/[0.08] text-white font-semibold rounded-lg transition-colors">Registrarse</a>
                <a href="/login" class="px-5 py-2.5 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors">Ingresar</a>
            </div>
            <button id="mobile-menu-btn" class="md:hidden text-slate-400" onclick="document.getElementById('mobile-nav').classList.toggle('hidden')">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </div>
        <div id="mobile-nav" class="hidden md:hidden px-4 pb-4 space-y-2 border-t border-white/[0.06]">
            <a href="#features" class="block py-2 text-slate-400 hover:text-white">Funciones</a>
            <a href="#modules" class="block py-2 text-slate-400 hover:text-white">Modulos</a>
            <a href="#plans" class="block py-2 text-slate-400 hover:text-white">Planes</a>
            <a href="#faq" class="block py-2 text-slate-400 hover:text-white">FAQ</a>
            <a href="/registro" class="block py-2 text-slate-400 hover:text-white">Registrarse</a>
            <a href="/login" class="block py-2 text-accent font-semibold">Ingresar</a>
        </div>
    </nav>

    <!-- Hero -->
    <section class="relative overflow-hidden">
        <div class="hero-glow absolute inset-0 pointer-events-none"></div>
        <div class="max-w-6xl mx-auto px-4 py-24 md:py-36 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-accent/10 border border-accent/20 text-accent text-sm font-medium mb-8">
                <i data-lucide="zap" class="w-4 h-4"></i>
                Plataforma SaaS Multi-Tenant
            </div>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-syne font-extrabold leading-tight mb-6">
                Gestiona y vende<br>
                <span class="gradient-text">servicios digitales</span><br>
                como un profesional
            </h1>
            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                Streaming, IPTV, software y mas. Tu propio panel con tienda publica, sistema de creditos, reportes avanzados y gestion completa de cuentas y perfiles.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/registro" class="px-8 py-4 bg-accent hover:bg-accent-hover text-white font-bold rounded-xl transition-all hover:scale-105 text-lg flex items-center gap-2">
                    Comenzar Ahora
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
                <a href="#modules" class="px-8 py-4 bg-white/[0.04] hover:bg-white/[0.08] border border-white/[0.08] text-white font-semibold rounded-xl transition-all text-lg flex items-center gap-2">
                    Ver Modulos
                    <i data-lucide="layout-grid" class="w-5 h-5"></i>
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-20 max-w-3xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl font-syne font-bold gradient-text stat-number">10+</div>
                    <div class="text-sm text-slate-500 mt-1">Modulos</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-syne font-bold gradient-text stat-number">5</div>
                    <div class="text-sm text-slate-500 mt-1">Roles</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-syne font-bold gradient-text stat-number">100%</div>
                    <div class="text-sm text-slate-500 mt-1">Personalizable</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-syne font-bold gradient-text stat-number">PWA</div>
                    <div class="text-sm text-slate-500 mt-1">Instalable</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-20 md:py-28">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-syne font-bold mb-4">Todo lo que necesitas en <span class="gradient-text">un solo lugar</span></h2>
                <p class="text-slate-400 max-w-xl mx-auto">Herramientas poderosas para gestionar tu negocio de servicios digitales de principio a fin.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card-feature rounded-2xl p-6">
                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center mb-4">
                        <i data-lucide="layers" class="w-6 h-6 text-accent"></i>
                    </div>
                    <h3 class="text-lg font-syne font-bold mb-2">Multi-Tenant</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Cada panel tiene su propio subdominio, datos aislados, tienda publica y configuracion independiente.</p>
                </div>
                <div class="card-feature rounded-2xl p-6">
                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center mb-4">
                        <i data-lucide="shield-check" class="w-6 h-6 text-accent"></i>
                    </div>
                    <h3 class="text-lg font-syne font-bold mb-2">Seguridad Avanzada</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Encriptacion AES-256-GCM para cuentas, bcrypt para passwords, CSRF en todos los formularios, rate limiting.</p>
                </div>
                <div class="card-feature rounded-2xl p-6">
                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center mb-4">
                        <i data-lucide="coins" class="w-6 h-6 text-accent"></i>
                    </div>
                    <h3 class="text-lg font-syne font-bold mb-2">Sistema de Creditos</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Flujo de creditos en 3 niveles: Super Admin vende a Paneles, Paneles generan a usuarios, usuarios acreditan a clientes.</p>
                </div>
                <div class="card-feature rounded-2xl p-6">
                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center mb-4">
                        <i data-lucide="store" class="w-6 h-6 text-accent"></i>
                    </div>
                    <h3 class="text-lg font-syne font-bold mb-2">Tienda Publica</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Cada panel tiene su tienda con catalogo, pagina de como comprar, FAQ y seguimiento de pedidos.</p>
                </div>
                <div class="card-feature rounded-2xl p-6">
                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center mb-4">
                        <i data-lucide="message-circle" class="w-6 h-6 text-accent"></i>
                    </div>
                    <h3 class="text-lg font-syne font-bold mb-2">WhatsApp Integrado</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">12 plantillas de mensajes configurables. Cobros, entregas, bienvenida, renovaciones y mas con un click.</p>
                </div>
                <div class="card-feature rounded-2xl p-6">
                    <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center mb-4">
                        <i data-lucide="bar-chart-3" class="w-6 h-6 text-accent"></i>
                    </div>
                    <h3 class="text-lg font-syne font-bold mb-2">Reportes y Metricas</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Dashboard con ingresos, costos, utilidad, retencion. Graficos interactivos con filtros por periodo.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules -->
    <section id="modules" class="py-20 md:py-28 border-t border-white/[0.06]">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-syne font-bold mb-4"><span class="gradient-text">10+ Modulos</span> listos para usar</h2>
                <p class="text-slate-400 max-w-xl mx-auto">Cada modulo esta disenado para cubrir una necesidad especifica de tu negocio.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php
                $modules = [
                    ['icon' => 'layout-dashboard', 'name' => 'Dashboard', 'desc' => 'Metricas en tiempo real, stock, cobros pendientes'],
                    ['icon' => 'tv', 'name' => 'Servicios', 'desc' => 'CRUD de servicios con precios y duraciones'],
                    ['icon' => 'key', 'name' => 'Cuentas', 'desc' => 'Gestion de cuentas con perfiles y encriptacion'],
                    ['icon' => 'user-check', 'name' => 'Perfiles', 'desc' => 'Asignacion automatica con bloqueo por concurrencia'],
                    ['icon' => 'shopping-cart', 'name' => 'Ventas', 'desc' => 'Crear, renovar, cobros grupales, vencimientos'],
                    ['icon' => 'users', 'name' => 'Clientes', 'desc' => 'Base de clientes con estados y wallet'],
                    ['icon' => 'truck', 'name' => 'Proveedores', 'desc' => 'Control de proveedores y total de cuentas'],
                    ['icon' => 'coins', 'name' => 'Creditos', 'desc' => 'Sistema de 3 niveles con movimientos'],
                    ['icon' => 'mail', 'name' => 'IMAP', 'desc' => 'Lectura automatica de codigos de verificacion'],
                    ['icon' => 'bar-chart-3', 'name' => 'Reportes', 'desc' => 'Ventas, vendedores, servicios, clientes'],
                    ['icon' => 'settings', 'name' => 'Configuracion', 'desc' => 'Moneda, tasa, pagos, plantillas WhatsApp'],
                    ['icon' => 'store', 'name' => 'Tienda', 'desc' => 'Landing, catalogo, FAQ, seguimiento'],
                ];
                foreach ($modules as $mod): ?>
                <div class="card-feature rounded-xl p-5 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center shrink-0 mt-0.5">
                        <i data-lucide="<?= $mod['icon'] ?>" class="w-5 h-5 text-accent"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-white text-sm"><?= $mod['name'] ?></h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed"><?= $mod['desc'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Role Hierarchy -->
    <section class="py-20 md:py-28 border-t border-white/[0.06]">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-syne font-bold mb-4">Jerarquia de <span class="gradient-text">roles inteligente</span></h2>
                <p class="text-slate-400 max-w-xl mx-auto">4 niveles de acceso con permisos granulares. Cada rol ve solo lo que necesita.</p>
            </div>
            <div class="flex flex-col md:flex-row items-center justify-center gap-3 md:gap-0">
                <?php
                $roles = [
                    ['name' => 'Panel', 'desc' => 'Dueno del negocio', 'color' => '#ea580c'],
                    ['name' => 'Distribuidor', 'desc' => 'Gestiona vendedores', 'color' => '#f97316'],
                    ['name' => 'Vendedor', 'desc' => 'Vende a clientes', 'color' => '#fb923c'],
                    ['name' => 'Cliente', 'desc' => 'Accede a su cuenta', 'color' => '#fdba74'],
                ];
                foreach ($roles as $i => $role): ?>
                <div class="flex items-center">
                    <div class="text-center px-4 py-5 rounded-xl bg-white/[0.03] border border-white/[0.06] min-w-[140px]">
                        <div class="w-10 h-10 rounded-full mx-auto mb-2 flex items-center justify-center" style="background: <?= $role['color'] ?>20; border: 2px solid <?= $role['color'] ?>">
                            <span class="text-sm font-bold" style="color: <?= $role['color'] ?>"><?= $i + 1 ?></span>
                        </div>
                        <div class="text-sm font-semibold text-white"><?= $role['name'] ?></div>
                        <div class="text-xs text-slate-500 mt-1"><?= $role['desc'] ?></div>
                    </div>
                    <?php if ($i < count($roles) - 1): ?>
                    <div class="hidden md:block text-slate-600 px-2"><i data-lucide="chevron-right" class="w-5 h-5"></i></div>
                    <div class="md:hidden text-slate-600 py-1"><i data-lucide="chevron-down" class="w-5 h-5"></i></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Plans -->
    <section id="plans" class="py-20 md:py-28 border-t border-white/[0.06]">
        <div class="max-w-6xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-syne font-bold mb-4">Planes que <span class="gradient-text">se adaptan a ti</span></h2>
                <p class="text-slate-400 max-w-xl mx-auto">Comienza con un trial gratuito y escala segun crece tu negocio.</p>
            </div>
            <div class="grid md:grid-cols-2 gap-6 max-w-3xl mx-auto">
                <!-- Estandar -->
                <div class="plan-card rounded-2xl p-8">
                    <h3 class="font-syne font-bold text-xl mb-2">Estandar</h3>
                    <p class="text-sm text-slate-500 mb-8">Todo lo esencial para gestionar tu negocio de servicios digitales.</p>
                    <ul class="space-y-3 mb-8 text-sm">
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> Gestion de cuentas y perfiles</li>
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> Ventas y renovaciones</li>
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> Clientes y proveedores</li>
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> Tienda publica</li>
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> WhatsApp integrado</li>
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> Dashboard y metricas</li>
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> Sistema de creditos</li>
                    </ul>
                    <a href="/registro?plan=estandar" class="block w-full py-3 text-center bg-white/[0.06] hover:bg-white/[0.1] border border-white/[0.08] text-white font-semibold rounded-lg transition-colors">Comenzar</a>
                </div>

                <!-- VIP -->
                <div class="plan-card popular rounded-2xl p-8 relative">
                    <div class="badge absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 rounded-full text-xs font-bold text-white">Recomendado</div>
                    <h3 class="font-syne font-bold text-xl mb-2">VIP</h3>
                    <p class="text-sm text-slate-500 mb-8">Sin limites. Para negocios que necesitan escalar al maximo.</p>
                    <ul class="space-y-3 mb-8 text-sm">
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> Todo lo del plan Estandar</li>
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> Cuentas y usuarios ilimitados</li>
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> IMAP para codigos de verificacion</li>
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> Reportes avanzados</li>
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> Distribuidores y vendedores</li>
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> Soporte prioritario</li>
                        <li class="flex items-center gap-2 text-slate-300"><i data-lucide="check" class="w-4 h-4 text-accent"></i> Personalizacion completa</li>
                    </ul>
                    <a href="/registro?plan=vip" class="block w-full py-3 text-center bg-accent hover:bg-accent-hover text-white font-bold rounded-lg transition-colors">Comenzar</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-20 md:py-28 border-t border-white/[0.06]">
        <div class="max-w-3xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-syne font-bold mb-4">Preguntas <span class="gradient-text">frecuentes</span></h2>
            </div>
            <div class="space-y-3">
                <?php
                $faqs = [
                    ['q' => 'Que tipo de servicios puedo vender?', 'a' => 'Streaming (Netflix, Disney+, Spotify, etc.), IPTV, licencias de software, VPN, y cualquier servicio digital que se gestione con cuentas y perfiles.'],
                    ['q' => 'Necesito conocimientos tecnicos?', 'a' => 'No. La plataforma esta disenada para ser intuitiva. Solo necesitas crear tus servicios, agregar cuentas y empezar a vender.'],
                    ['q' => 'Puedo personalizar mi tienda?', 'a' => 'Si. Puedes cambiar colores, logo, mensajes de WhatsApp, datos de pago, FAQ y descripcion. Todo desde el panel de configuracion.'],
                    ['q' => 'Como funciona el sistema de creditos?', 'a' => 'Los creditos fluyen de arriba a abajo: Super Admin vende creditos a Paneles, los Paneles los distribuyen a sus vendedores, y los vendedores los usan para acreditar wallets de clientes.'],
                    ['q' => 'Hay periodo de prueba?', 'a' => 'Si, todos los planes incluyen un trial gratuito de 3 dias para que pruebes todas las funcionalidades.'],
                    ['q' => 'Mis datos estan seguros?', 'a' => 'Absolutamente. Usamos encriptacion AES-256-GCM para credenciales de cuentas, bcrypt con cost 12 para passwords de usuarios, proteccion CSRF en todos los formularios y rate limiting contra ataques de fuerza bruta.'],
                ];
                foreach ($faqs as $i => $faq): ?>
                <div class="border border-white/[0.06] rounded-xl overflow-hidden">
                    <button onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.faq-icon').classList.toggle('rotate-45')"
                            class="w-full flex items-center justify-between p-5 text-left hover:bg-white/[0.02] transition-colors">
                        <span class="text-sm font-semibold text-white pr-4"><?= $faq['q'] ?></span>
                        <i data-lucide="plus" class="w-5 h-5 text-slate-500 shrink-0 faq-icon transition-transform duration-200"></i>
                    </button>
                    <div class="hidden px-5 pb-5">
                        <p class="text-sm text-slate-400 leading-relaxed"><?= $faq['a'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-20 md:py-28 border-t border-white/[0.06]">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <div class="max-w-2xl mx-auto bg-gradient-to-b from-accent/10 to-transparent border border-accent/20 rounded-3xl p-12">
                <h2 class="text-3xl md:text-4xl font-syne font-bold mb-4">Listo para empezar?</h2>
                <p class="text-slate-400 mb-8">Crea tu panel en minutos y comienza a gestionar tus servicios digitales como un profesional.</p>
                <a href="/registro" class="inline-flex items-center gap-2 px-8 py-4 bg-accent hover:bg-accent-hover text-white font-bold rounded-xl transition-all hover:scale-105 text-lg">
                    Crear mi Panel
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-white/[0.06]">
        <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="/assets/img/logo/logo-icon.svg" alt="IronClick" class="h-7 w-7">
                    <span class="font-orbitron font-bold text-sm gradient-text">IronClick</span>
                </div>
                <div class="flex items-center gap-6 text-sm text-slate-500">
                    <a href="#features" class="hover:text-white transition-colors">Funciones</a>
                    <a href="#modules" class="hover:text-white transition-colors">Modulos</a>
                    <a href="#plans" class="hover:text-white transition-colors">Planes</a>
                    <a href="/login" class="hover:text-white transition-colors">Ingresar</a>
                </div>
            </div>
            <p class="text-xs text-slate-600 text-center mt-6">IronClick.app - Todos los derechos reservados</p>
        </div>
    </footer>

    <script>lucide.createIcons();</script>
    <script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            e.preventDefault();
            const target = document.querySelector(a.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            document.getElementById('mobile-nav')?.classList.add('hidden');
        });
    });
    </script>
</body>
</html>
