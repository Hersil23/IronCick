<?php ob_start(); ?>

<h1 class="text-3xl font-bold font-['Syne'] mb-8">Como Comprar</h1>

<div class="max-w-2xl mx-auto space-y-8">
    <div class="flex gap-4">
        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold shrink-0" style="background:var(--tienda-primario)">1</div>
        <div>
            <h3 class="text-lg font-semibold mb-2">Elige tu servicio</h3>
            <p class="text-slate-400">Revisa nuestro <a href="/catalogo" class="underline" style="color:var(--tienda-primario)">catalogo</a> y elige el servicio que te interesa.</p>
        </div>
    </div>

    <div class="flex gap-4">
        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold shrink-0" style="background:var(--tienda-primario)">2</div>
        <div>
            <h3 class="text-lg font-semibold mb-2">Contactanos por WhatsApp</h3>
            <p class="text-slate-400">Escribe al WhatsApp indicando que servicio deseas y te daremos las instrucciones de pago.</p>
        </div>
    </div>

    <div class="flex gap-4">
        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold shrink-0" style="background:var(--tienda-primario)">3</div>
        <div>
            <h3 class="text-lg font-semibold mb-2">Realiza el pago</h3>
            <p class="text-slate-400">Transfiere el monto a nuestros datos bancarios y envia el comprobante.</p>
            <?php if (!empty($config['banco'])): ?>
            <div class="mt-3 p-4 bg-white/[0.04] border border-white/[0.08] rounded-xl text-sm">
                <p><span class="text-muted">Banco:</span> <?= htmlspecialchars($config['banco']) ?></p>
                <?php if (!empty($config['telefono_pago'])): ?>
                <p><span class="text-muted">Pago movil:</span> <?= htmlspecialchars($config['telefono_pago']) ?></p>
                <?php endif; ?>
                <?php if (!empty($config['cuenta_banco'])): ?>
                <p><span class="text-muted">Cuenta:</span> <?= htmlspecialchars($config['cuenta_banco']) ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex gap-4">
        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold shrink-0" style="background:var(--tienda-primario)">4</div>
        <div>
            <h3 class="text-lg font-semibold mb-2">Recibe tus credenciales</h3>
            <p class="text-slate-400">Una vez confirmado el pago, recibiras tus datos de acceso por WhatsApp al instante.</p>
        </div>
    </div>
</div>

<?php if (!empty($config['whatsapp_contacto'])): ?>
<div class="text-center mt-12">
    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $config['whatsapp_contacto']) ?>" target="_blank"
       class="inline-block px-8 py-3 bg-green-600 hover:bg-green-700 rounded-lg text-white font-semibold transition-colors">
        Contactar por WhatsApp
    </a>
</div>
<?php endif; ?>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/tienda.php'; ?>
