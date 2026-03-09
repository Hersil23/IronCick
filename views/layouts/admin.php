<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($pageTitle ?? 'IronClick') ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/img/logo/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/img/logo/favicon-32x32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/img/logo/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#060608">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        base: '#060608',
                        surface: 'rgba(255,255,255,0.04)',
                        elevated: 'rgba(255,255,255,0.07)',
                        border: 'rgba(255,255,255,0.08)',
                        accent: '#ea580c',
                        'accent-hover': '#f97316',
                        muted: '#64748b',
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@600;700;800&family=Orbitron:wght@700&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>

    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-base text-slate-200 font-['DM_Sans'] min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-base border-r border-white/[0.08] transform -translate-x-full lg:translate-x-0 transition-transform duration-200">
            <div class="flex items-center gap-3 px-6 py-5 border-b border-white/[0.08]">
                <img src="/assets/img/logo/logo-full.svg" alt="IronClick" class="h-10">
            </div>

            <nav class="mt-4 px-3 space-y-1">
                <?php
                $menuItems = [
                    ['dashboard', 'layout-dashboard', 'Dashboard'],
                    ['servicios', 'package', 'Servicios'],
                    ['cuentas', 'key-round', 'Cuentas'],
                    ['proveedores', 'truck', 'Proveedores'],
                    ['clientes', 'users', 'Clientes'],
                    ['ventas', 'shopping-cart', 'Ventas'],
                    ['creditos', 'wallet', 'Creditos'],
                    ['reportes', 'bar-chart-3', 'Reportes'],
                    ['configuracion', 'settings', 'Configuracion'],
                ];

                if (Auth::isPanel() || Auth::isSuperAdmin()) {
                    $menuItems[] = ['vendedores', 'user-check', 'Vendedores'];
                }
                if (Auth::isPanel()) {
                    $menuItems[] = ['distribuidores', 'network', 'Distribuidores'];
                }

                foreach ($menuItems as $item):
                    $active = ($currentPage ?? '') === $item[0];
                ?>
                <a href="/<?= $item[0] ?>"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors <?= $active ? 'bg-accent/10 text-accent' : 'text-slate-400 hover:text-slate-200 hover:bg-white/[0.04]' ?>">
                    <i data-lucide="<?= $item[1] ?>" class="w-5 h-5"></i>
                    <span><?= $item[2] ?></span>
                </a>
                <?php endforeach; ?>
            </nav>

            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-white/[0.08]">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="w-8 h-8 rounded-full bg-accent/20 flex items-center justify-center text-accent text-sm font-semibold">
                        <?= strtoupper(substr(Auth::nombre(), 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate"><?= htmlspecialchars(Auth::nombre()) ?></p>
                        <p class="text-xs text-muted truncate"><?= ucfirst(Auth::rol()) ?></p>
                    </div>
                    <button onclick="logout()" class="text-muted hover:text-red-400 transition-colors">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Overlay mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64">
            <!-- Top bar -->
            <header class="sticky top-0 z-30 bg-base/80 backdrop-blur-xl border-b border-white/[0.08]">
                <div class="flex items-center justify-between px-4 lg:px-8 py-4">
                    <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div class="flex items-center gap-4">
                        <button onclick="openListaPrecios()" class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium transition-colors">
                            <i data-lucide="list" class="w-4 h-4"></i>
                            Lista de Precios
                        </button>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <div class="p-4 lg:p-8">
                <?php
                $viewFile = $viewMap[$currentPage] ?? null;
                if ($viewFile && file_exists(__DIR__ . '/../../' . $viewFile)) {
                    include __DIR__ . '/../../' . $viewFile;
                }
                ?>
            </div>
        </main>
    </div>

    <!-- Modal Lista de Precios -->
    <div id="modal-precios" class="fixed inset-0 z-[60] hidden">
        <div class="absolute inset-0 bg-black/60" onclick="closeModal('modal-precios')"></div>
        <div class="relative z-10 max-w-lg mx-auto mt-20 bg-[#0a0a0f] border border-white/[0.08] rounded-2xl p-6 backdrop-blur-xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold font-['Syne']">Lista de Precios</h3>
                <button onclick="closeModal('modal-precios')" class="text-muted hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <div id="lista-precios-content" class="space-y-3"></div>
            <div class="mt-6 flex gap-3">
                <button onclick="copiarListaPrecios()" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <i data-lucide="copy" class="w-4 h-4"></i> Copiar para WhatsApp
                </button>
                <button onclick="compartirImagen()" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium transition-colors">
                    <i data-lucide="image" class="w-4 h-4"></i> Compartir como Imagen
                </button>
            </div>
        </div>
    </div>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= CSRF::generate() ?>">

    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/search.js"></script>
    <script src="/assets/js/mensajes.js"></script>
    <?php if (($currentPage ?? '') === 'dashboard' || ($currentPage ?? '') === 'reportes'): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script src="/assets/js/charts.js"></script>
    <?php endif; ?>
    <script>lucide.createIcons();</script>
</body>
</html>
