<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Tienda') ?></title>

    <?php
    $colores = json_decode($config['tienda_colores_json'] ?? '{}', true);
    $primario = $colores['primario'] ?? '#ea580c';
    $secundario = $colores['secundario'] ?? '#f97316';
    $redes = json_decode($config['tienda_redes_json'] ?? '{}', true);
    ?>

    <!-- SEO -->
    <meta name="description" content="<?= htmlspecialchars($config['tienda_descripcion'] ?? $panel['nombre'] . ' - Servicios digitales') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($config['tienda_descripcion'] ?? '') ?>">
    <?php if (!empty($config['tienda_logo'])): ?>
    <meta property="og:image" content="/uploads/<?= $config['tienda_logo'] ?>">
    <?php endif; ?>
    <link rel="canonical" href="https://<?= htmlspecialchars($panel['subdominio']) ?>.ironclick.app<?= $_SERVER['REQUEST_URI'] ?>">

    <!-- Schema.org -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "<?= htmlspecialchars($panel['nombre']) ?>",
        "url": "https://<?= htmlspecialchars($panel['subdominio']) ?>.ironclick.app"
    }
    </script>

    <link rel="icon" type="image/x-icon" href="/assets/img/logo/favicon.ico">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#060608">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.min.js"></script>
    <link rel="stylesheet" href="/assets/css/app.css">

    <style>
        :root {
            --tienda-primario: <?= htmlspecialchars($primario) ?>;
            --tienda-secundario: <?= htmlspecialchars($secundario) ?>;
        }
    </style>
</head>
<body class="bg-[#060608] text-slate-200 font-['DM_Sans'] min-h-screen">
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 bg-[#060608]/80 backdrop-blur-xl border-b border-white/[0.08]">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <?php if (!empty($config['tienda_logo'])): ?>
                <img src="/uploads/<?= htmlspecialchars($config['tienda_logo']) ?>" alt="<?= htmlspecialchars($panel['nombre']) ?>" class="h-10 rounded-lg">
                <?php endif; ?>
                <span class="text-lg font-bold font-['Syne']"><?= htmlspecialchars($panel['nombre']) ?></span>
            </a>
            <div class="hidden md:flex items-center gap-6 text-sm">
                <a href="/" class="text-slate-400 hover:text-white transition-colors">Inicio</a>
                <a href="/catalogo" class="text-slate-400 hover:text-white transition-colors">Catalogo</a>
                <a href="/como-comprar" class="text-slate-400 hover:text-white transition-colors">Como Comprar</a>
                <a href="/faq" class="text-slate-400 hover:text-white transition-colors">FAQ</a>
                <a href="/mi-cuenta" class="px-4 py-2 rounded-lg text-white text-sm font-medium transition-colors" style="background:var(--tienda-primario)">Mi Cuenta</a>
            </div>
            <button id="mobile-menu-btn" class="md:hidden text-slate-400" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </div>
        <div id="mobile-menu" class="hidden md:hidden px-4 pb-4 space-y-2">
            <a href="/" class="block py-2 text-slate-400 hover:text-white">Inicio</a>
            <a href="/catalogo" class="block py-2 text-slate-400 hover:text-white">Catalogo</a>
            <a href="/como-comprar" class="block py-2 text-slate-400 hover:text-white">Como Comprar</a>
            <a href="/faq" class="block py-2 text-slate-400 hover:text-white">FAQ</a>
            <a href="/mi-cuenta" class="block py-2 text-white" style="color:var(--tienda-primario)">Mi Cuenta</a>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/[0.08] mt-16">
        <div class="max-w-6xl mx-auto px-4 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm text-muted"><?= htmlspecialchars($panel['nombre']) ?> - Todos los derechos reservados</p>
                <div class="flex items-center gap-4">
                    <?php if (!empty($redes['instagram'])): ?>
                    <a href="<?= htmlspecialchars($redes['instagram']) ?>" target="_blank" class="text-muted hover:text-white"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($config['whatsapp_contacto'])): ?>
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $config['whatsapp_contacto']) ?>" target="_blank" class="text-muted hover:text-green-400"><i data-lucide="message-circle" class="w-5 h-5"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <p class="text-xs text-muted/50 text-center mt-4">Powered by IronClick</p>
        </div>
    </footer>

    <script>lucide.createIcons();</script>
</body>
</html>
