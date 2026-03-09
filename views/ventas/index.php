<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold font-['Syne']">Ventas</h2>
    <button onclick="openModal('modal-venta')" class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Nueva Venta
    </button>
</div>

<!-- Tabs -->
<div class="flex flex-wrap gap-2 mb-4" id="ventas-tabs">
    <button onclick="filtrarTab('')" data-tab="" class="tab-btn px-3 py-1.5 rounded-lg text-sm bg-accent text-white">Todas <span id="tab-todas">0</span></button>
    <button onclick="filtrarTab('activas')" data-tab="activas" class="tab-btn px-3 py-1.5 rounded-lg text-sm bg-white/[0.04] text-slate-400">Activas <span id="tab-activas">0</span></button>
    <button onclick="filtrarTab('por_vencer')" data-tab="por_vencer" class="tab-btn px-3 py-1.5 rounded-lg text-sm bg-white/[0.04] text-slate-400">Por vencer <span id="tab-por_vencer">0</span></button>
    <button onclick="filtrarTab('vencidas')" data-tab="vencidas" class="tab-btn px-3 py-1.5 rounded-lg text-sm bg-white/[0.04] text-slate-400">Vencidas <span id="tab-vencidas">0</span></button>
    <button onclick="filtrarTab('vencidas_7')" data-tab="vencidas_7" class="tab-btn px-3 py-1.5 rounded-lg text-sm bg-white/[0.04] text-slate-400">Vencidas +7d <span id="tab-vencidas_7">0</span></button>
</div>

<!-- Filtros -->
<div class="flex flex-wrap gap-3 mb-6">
    <input type="text" id="venta-search" placeholder="Buscar nombre, telefono, servicio, cuenta, PIN, orden..."
           class="input-field flex-1 min-w-[200px]" oninput="debounceSearch(cargarVentas, 300)">
    <select id="venta-servicio" class="input-field" onchange="cargarVentas()">
        <option value="">Todos los servicios</option>
    </select>
</div>

<div class="glass rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-muted border-b border-white/[0.08]">
                    <th class="pb-3">#</th>
                    <th class="pb-3">Cliente</th>
                    <th class="pb-3">Servicio</th>
                    <th class="pb-3">Cuenta/Perfil</th>
                    <th class="pb-3">Precio</th>
                    <th class="pb-3">Vence</th>
                    <th class="pb-3">Saldo</th>
                    <th class="pb-3">Vendedor</th>
                    <th class="pb-3">WhatsApp</th>
                    <th class="pb-3">Acciones</th>
                </tr>
            </thead>
            <tbody id="ventas-table"></tbody>
        </table>
    </div>
    <div id="ventas-paginacion" class="mt-4"></div>
</div>

<!-- Modal Nueva Venta -->
<div id="modal-venta" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('modal-venta')"></div>
    <div class="relative z-10 max-w-lg mx-auto mt-10 bg-[#0a0a0f] border border-white/[0.08] rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-6">Nueva Venta</h3>
        <form id="form-venta" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-400 mb-1">Cliente</label>
                <select name="cliente_id" required class="input-field w-full" id="venta-cliente-select"></select>
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Servicio</label>
                <select name="servicio_id" required class="input-field w-full" id="venta-servicio-select"></select>
            </div>
            <p class="text-xs text-muted">El sistema asignara automaticamente un perfil disponible.</p>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-venta')" class="flex-1 px-4 py-2.5 bg-white/[0.04] text-slate-400 rounded-lg text-sm">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Vender</button>
            </div>
        </form>
    </div>
</div>

<script>
let ventasTab = '';
let ventasPagina = 1;

document.addEventListener('DOMContentLoaded', async function() {
    // Load service dropdown
    const sRes = await apiGet('/api/servicios');
    if (sRes.success) {
        const opts = sRes.data.filter(s => s.estado === 'activo').map(s => `<option value="${s.id}">${s.nombre} ($${s.precio_usd}) - ${s.disponibles} disp.</option>`).join('');
        document.getElementById('venta-servicio').innerHTML = '<option value="">Todos</option>' + opts;
        document.getElementById('venta-servicio-select').innerHTML = '<option value="">Seleccionar...</option>' + opts;
    }

    // Load clients dropdown
    const cRes = await apiGet('/api/clientes?estado=activo');
    if (cRes.success) {
        document.getElementById('venta-cliente-select').innerHTML = '<option value="">Seleccionar...</option>' +
            cRes.data.clientes.map(c => `<option value="${c.id}">${c.nombre} ${c.apellido || ''} - ${c.telefono || ''}</option>`).join('');
    }

    cargarVentas();
});

function filtrarTab(tab) {
    ventasTab = tab;
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('bg-accent', 'text-white');
        b.classList.add('bg-white/[0.04]', 'text-slate-400');
    });
    const active = document.querySelector(`[data-tab="${tab}"]`);
    active.classList.add('bg-accent', 'text-white');
    active.classList.remove('bg-white/[0.04]', 'text-slate-400');
    cargarVentas();
}

async function cargarVentas(pagina) {
    ventasPagina = pagina || 1;
    const q = document.getElementById('venta-search').value;
    const servicio = document.getElementById('venta-servicio').value;

    const res = await apiGet(`/api/ventas?tab=${ventasTab}&q=${encodeURIComponent(q)}&servicio=${servicio}&pagina=${ventasPagina}`);
    if (!res.success) return;

    // Update tab counts
    if (res.data.tabs) {
        const t = res.data.tabs;
        document.getElementById('tab-todas').textContent = t.todas || 0;
        document.getElementById('tab-activas').textContent = t.activas || 0;
        document.getElementById('tab-por_vencer').textContent = t.por_vencer || 0;
        document.getElementById('tab-vencidas').textContent = t.vencidas || 0;
        document.getElementById('tab-vencidas_7').textContent = t.vencidas_7 || 0;
    }

    document.getElementById('ventas-table').innerHTML = res.data.ventas.map((v, i) => {
        const estadoClass = v.estado === 'activa' ? 'bg-green-500/10 text-green-400' : v.estado === 'vencida' ? 'bg-red-500/10 text-red-400' : 'bg-slate-500/10 text-slate-400';
        const tel = (v.cliente_telefono || '').replace(/\D/g, '');

        return `<tr class="border-b border-white/[0.04]">
            <td class="py-3 text-muted text-xs">${v.numero_orden}</td>
            <td class="py-3"><p class="font-medium">${v.cliente_nombre}</p><p class="text-xs text-muted">${v.cliente_telefono || ''}</p></td>
            <td class="py-3">${v.servicio_nombre}</td>
            <td class="py-3"><p class="text-xs">${v.cuenta_correo}</p><p class="text-xs text-muted">P${v.numero_perfil} ${v.pin ? '| PIN: ' + v.pin : ''}</p></td>
            <td class="py-3">$${parseFloat(v.precio_usd).toFixed(2)}</td>
            <td class="py-3"><span class="px-2 py-1 rounded text-xs ${estadoClass}">${v.fecha_vencimiento}</span></td>
            <td class="py-3 text-muted">$${parseFloat(v.saldo_cliente || 0).toFixed(2)}</td>
            <td class="py-3 text-xs text-muted">${v.vendedor_nombre || 'Panel'}</td>
            <td class="py-3">
                <div class="flex gap-1">
                    <a href="https://wa.me/${tel}" target="_blank" class="text-green-400 hover:text-green-300 p-1" title="WhatsApp"><i data-lucide="message-circle" class="w-4 h-4"></i></a>
                </div>
            </td>
            <td class="py-3">
                <div class="flex gap-1">
                    <button onclick="renovarVenta(${v.id})" class="text-muted hover:text-green-400 p-1" title="Renovar"><i data-lucide="refresh-cw" class="w-4 h-4"></i></button>
                    <button onclick="eliminarVenta(${v.id})" class="text-muted hover:text-red-400 p-1" title="Eliminar"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                </div>
            </td>
        </tr>`;
    }).join('');

    renderPaginacion('ventas-paginacion', res.data.paginacion, cargarVentas);
    lucide.createIcons();
}

async function renovarVenta(id) {
    if (!confirm('Renovar esta venta?')) return;
    await apiPut(`/api/ventas/${id}/renovar`, {});
    cargarVentas();
}

async function eliminarVenta(id) {
    if (!confirm('Eliminar esta venta? El perfil quedara disponible.')) return;
    await apiDelete(`/api/ventas/${id}`);
    cargarVentas();
}

document.getElementById('form-venta').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this));
    const res = await apiPost('/api/ventas', data);
    if (res.success) {
        closeModal('modal-venta');
        this.reset();
        cargarVentas();
        showToast('Venta creada. Perfil asignado automaticamente.');
    }
});
</script>
