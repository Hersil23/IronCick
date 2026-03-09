<?php ob_start(); ?>

<!-- Hero -->
<section class="text-center py-16">
    <?php if (!empty($config['tienda_logo'])): ?>
    <img src="/uploads/<?= htmlspecialchars($config['tienda_logo']) ?>" alt="<?= htmlspecialchars($panel['nombre']) ?>" class="h-24 mx-auto mb-6 rounded-2xl">
    <?php endif; ?>
    <h1 class="text-4xl md:text-5xl font-bold font-['Syne'] mb-4"><?= htmlspecialchars($panel['nombre']) ?></h1>
    <p class="text-lg text-slate-400 max-w-2xl mx-auto mb-8"><?= htmlspecialchars($config['tienda_bienvenida'] ?? $config['tienda_descripcion'] ?? 'Servicios digitales de calidad') ?></p>
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="/catalogo" class="px-8 py-3 rounded-lg text-white font-semibold transition-colors" style="background:var(--tienda-primario)">Ver Catalogo</a>
        <?php if (!empty($config['whatsapp_contacto'])): ?>
        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $config['whatsapp_contacto']) ?>" target="_blank"
           class="px-8 py-3 bg-green-600 hover:bg-green-700 rounded-lg text-white font-semibold transition-colors">Contactar por WhatsApp</a>
        <?php endif; ?>
    </div>
</section>

<!-- Servicios destacados -->
<?php if (!empty($servicios)): ?>
<section class="py-12">
    <h2 class="text-2xl font-bold font-['Syne'] text-center mb-8">Nuestros Servicios</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($servicios as $s): ?>
        <div class="bg-white/[0.04] border border-white/[0.08] rounded-2xl p-6 hover:border-white/[0.15] transition-colors">
            <?php if (!empty($s['imagen'])): ?>
            <img src="/uploads/<?= htmlspecialchars($s['imagen']) ?>" alt="<?= htmlspecialchars($s['nombre']) ?>" class="w-16 h-16 rounded-xl mb-4 object-cover">
            <?php endif; ?>
            <h3 class="text-lg font-semibold mb-2"><?= htmlspecialchars($s['nombre']) ?></h3>
            <p class="text-sm text-slate-400 mb-4"><?= htmlspecialchars($s['descripcion'] ?? '') ?></p>
            <div class="flex items-center justify-between">
                <span class="text-xl font-bold" style="color:var(--tienda-primario)">$<?= number_format($s['precio_usd'], 2) ?></span>
                <span class="text-xs text-muted"><?= $s['duracion_dias'] ?> dias</span>
            </div>
            <span class="text-xs mt-2 inline-block ${(int)$s['disponibles'] > 0 ? 'text-green-400' : 'text-red-400'}"><?= (int)$s['disponibles'] > 0 ? $s['disponibles'] . ' disponibles' : 'Agotado' ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/tienda.php'; ?>
