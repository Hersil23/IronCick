<?php ob_start(); ?>

<h1 class="text-3xl font-bold font-['Syne'] mb-8">Seguimiento de Pedido</h1>

<div class="max-w-lg mx-auto">
    <?php if ($venta): ?>
    <div class="bg-white/[0.04] border border-white/[0.08] rounded-2xl p-6">
        <div class="text-center mb-6">
            <span class="px-3 py-1 rounded-lg text-sm font-medium <?= $venta['estado'] === 'activa' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' ?>">
                <?= ucfirst($venta['estado']) ?>
            </span>
        </div>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-muted">Orden</span>
                <span class="font-medium"><?= htmlspecialchars($venta['numero_orden']) ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted">Servicio</span>
                <span><?= htmlspecialchars($venta['servicio_nombre']) ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted">Fecha de compra</span>
                <span><?= $venta['fecha_compra'] ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted">Vencimiento</span>
                <span><?= $venta['fecha_vencimiento'] ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-muted">Precio</span>
                <span class="font-bold" style="color:var(--tienda-primario)">$<?= number_format($venta['precio_usd'], 2) ?></span>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="text-center py-12">
        <p class="text-slate-400 mb-4">Pedido no encontrado</p>
        <p class="text-sm text-muted">Verifica el numero de orden e intenta de nuevo.</p>
    </div>
    <?php endif; ?>

    <!-- Buscar otro pedido -->
    <div class="mt-8">
        <form onsubmit="buscarPedido(event)" class="flex gap-3">
            <input type="text" id="buscar-orden" placeholder="Numero de orden (IC-XXXXXXXX)" class="flex-1 px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-[var(--tienda-primario)]">
            <button type="submit" class="px-6 py-3 rounded-lg text-white font-medium" style="background:var(--tienda-primario)">Buscar</button>
        </form>
    </div>
</div>

<script>
function buscarPedido(e) {
    e.preventDefault();
    const orden = document.getElementById('buscar-orden').value.trim();
    if (orden) window.location.href = '/pedido/' + encodeURIComponent(orden);
}
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/tienda.php'; ?>
