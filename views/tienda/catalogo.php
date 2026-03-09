<?php ob_start(); ?>

<h1 class="text-3xl font-bold font-['Syne'] mb-8">Catalogo</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($servicios as $s): ?>
    <div class="bg-white/[0.04] border border-white/[0.08] rounded-2xl p-6 hover:border-white/[0.15] transition-colors">
        <?php if (!empty($s['imagen'])): ?>
        <img src="/uploads/<?= htmlspecialchars($s['imagen']) ?>" alt="<?= htmlspecialchars($s['nombre']) ?>" class="w-full h-40 rounded-xl mb-4 object-cover">
        <?php endif; ?>
        <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($s['nombre']) ?></h3>
        <p class="text-sm text-slate-400 mb-4"><?= htmlspecialchars($s['descripcion'] ?? '') ?></p>
        <div class="flex items-center justify-between mb-4">
            <span class="text-2xl font-bold" style="color:var(--tienda-primario)">$<?= number_format($s['precio_usd'], 2) ?></span>
            <span class="text-sm text-muted"><?= $s['duracion_dias'] ?> dias</span>
        </div>
        <?php if ((int)$s['disponibles'] > 0 && !empty($config['whatsapp_contacto'])): ?>
        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $config['whatsapp_contacto']) ?>?text=<?= rawurlencode('Hola, me interesa el servicio de ' . $s['nombre']) ?>"
           target="_blank" class="block text-center py-2.5 rounded-lg text-white font-medium transition-colors" style="background:var(--tienda-primario)">
            Solicitar
        </a>
        <?php else: ?>
        <span class="block text-center py-2.5 rounded-lg bg-slate-700/50 text-slate-400">Agotado</span>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($servicios)): ?>
<p class="text-center text-slate-400 py-12">No hay servicios disponibles en este momento.</p>
<?php endif; ?>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/tienda.php'; ?>
