<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold font-['Syne']">Creditos</h2>
    <button onclick="openModal('modal-credito')" class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Acreditar
    </button>
</div>

<div class="glass rounded-2xl p-6">
    <h3 class="text-lg font-semibold font-['Syne'] mb-4">Movimientos</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-muted border-b border-white/[0.08]">
                    <th class="pb-3">Fecha</th>
                    <th class="pb-3">Tipo</th>
                    <th class="pb-3">Origen</th>
                    <th class="pb-3">Destino</th>
                    <th class="pb-3">Monto</th>
                    <th class="pb-3">Concepto</th>
                </tr>
            </thead>
            <tbody id="creditos-table"></tbody>
        </table>
    </div>
    <div id="creditos-paginacion" class="mt-4"></div>
</div>

<!-- Modal Acreditar -->
<div id="modal-credito" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('modal-credito')"></div>
    <div class="relative z-10 max-w-md mx-auto mt-20 bg-[#0a0a0f] border border-white/[0.08] rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-6">Acreditar a Cliente</h3>
        <form id="form-credito" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-400 mb-1">Cliente ID</label>
                <input type="number" name="cliente_id" required class="input-field w-full">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Monto USD</label>
                <input type="number" step="0.01" name="monto" required class="input-field w-full">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-credito')" class="flex-1 px-4 py-2.5 bg-white/[0.04] text-slate-400 rounded-lg text-sm">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Acreditar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', cargarCreditos);

async function cargarCreditos(pagina) {
    const p = pagina || 1;
    const res = await apiGet(`/api/creditos/movimientos?pagina=${p}`);
    if (!res.success) return;

    document.getElementById('creditos-table').innerHTML = res.data.movimientos.map(m => `
        <tr class="border-b border-white/[0.04]">
            <td class="py-3 text-muted text-xs">${m.created_at}</td>
            <td class="py-3"><span class="px-2 py-1 rounded text-xs ${m.tipo === 'entrada' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400'}">${m.tipo}</span></td>
            <td class="py-3">${m.origen}</td>
            <td class="py-3">${m.destino_tipo} #${m.destino_id}</td>
            <td class="py-3 font-medium ${m.tipo === 'entrada' ? 'text-green-400' : 'text-red-400'}">$${parseFloat(m.monto).toFixed(2)}</td>
            <td class="py-3 text-muted text-xs">${m.concepto || ''}</td>
        </tr>
    `).join('');

    renderPaginacion('creditos-paginacion', res.data.paginacion, cargarCreditos);
}

document.getElementById('form-credito').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this));
    await apiPost('/api/creditos/acreditar', data);
    closeModal('modal-credito');
    this.reset();
    cargarCreditos();
    showToast('Creditos acreditados.');
});
</script>
