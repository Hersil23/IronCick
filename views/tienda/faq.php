<?php ob_start(); ?>

<h1 class="text-3xl font-bold font-['Syne'] mb-8">Preguntas Frecuentes</h1>

<div class="max-w-2xl mx-auto space-y-4">
    <?php if (!empty($faqs)): ?>
        <?php foreach ($faqs as $i => $faq): ?>
        <div class="bg-white/[0.04] border border-white/[0.08] rounded-xl overflow-hidden">
            <button onclick="toggleFaq(<?= $i ?>)" class="w-full flex items-center justify-between p-5 text-left">
                <span class="font-medium"><?= htmlspecialchars($faq['pregunta'] ?? '') ?></span>
                <svg id="faq-icon-<?= $i ?>" class="w-5 h-5 text-muted transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div id="faq-body-<?= $i ?>" class="hidden px-5 pb-5 text-slate-400 text-sm">
                <?= htmlspecialchars($faq['respuesta'] ?? '') ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center text-slate-400 py-12">No hay preguntas frecuentes configuradas.</p>
    <?php endif; ?>
</div>

<script>
function toggleFaq(i) {
    const body = document.getElementById('faq-body-' + i);
    const icon = document.getElementById('faq-icon-' + i);
    body.classList.toggle('hidden');
    icon.style.transform = body.classList.contains('hidden') ? '' : 'rotate(180deg)';
}
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/tienda.php'; ?>
