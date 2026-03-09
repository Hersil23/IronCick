<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold font-['Syne']">Cuentas</h2>
    <button onclick="openModal('modal-cuenta')" class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Nueva Cuenta
    </button>
</div>

<!-- Filtros -->
<div class="flex flex-wrap gap-3 mb-6">
    <input type="text" id="cuenta-search" placeholder="Buscar cuenta, servicio o proveedor..."
           class="input-field flex-1 min-w-[200px]" oninput="debounceSearch(cargarCuentas, 300)">
    <select id="cuenta-servicio" class="input-field" onchange="cargarCuentas()">
        <option value="">Todos los servicios</option>
    </select>
    <select id="cuenta-estado" class="input-field" onchange="cargarCuentas()">
        <option value="">Todos los estados</option>
        <option value="activa">Activa</option>
        <option value="vencida">Vencida</option>
        <option value="suspendida">Suspendida</option>
    </select>
</div>

<div class="glass rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-muted border-b border-white/[0.08]">
                    <th class="pb-3">#</th>
                    <th class="pb-3">Cuenta</th>
                    <th class="pb-3">Servicio</th>
                    <th class="pb-3">Proveedor</th>
                    <th class="pb-3">Perfiles</th>
                    <th class="pb-3">Costo</th>
                    <th class="pb-3">Vence</th>
                    <th class="pb-3">Estado</th>
                    <th class="pb-3">Acciones</th>
                </tr>
            </thead>
            <tbody id="cuentas-table"></tbody>
        </table>
    </div>
    <div id="cuentas-paginacion" class="mt-4"></div>
</div>

<!-- Modal Crear Cuenta -->
<div id="modal-cuenta" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('modal-cuenta')"></div>
    <div class="relative z-10 max-w-lg mx-auto mt-10 bg-[#0a0a0f] border border-white/[0.08] rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-6">Nueva Cuenta</h3>
        <form id="form-cuenta" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-400 mb-1">Servicio</label>
                <select name="servicio_id" required class="input-field w-full" id="cuenta-servicio-select"></select>
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Correo de la cuenta</label>
                <input type="text" name="correo" required class="input-field w-full" placeholder="email@ejemplo.com">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Contrasena</label>
                <input type="text" name="password" required class="input-field w-full" placeholder="Contrasena de la cuenta">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Costo USD</label>
                    <input type="number" step="0.01" name="costo_usd" required class="input-field w-full" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Fecha Vencimiento</label>
                    <input type="date" name="fecha_vencimiento" required class="input-field w-full">
                </div>
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Proveedor (opcional)</label>
                <select name="proveedor_id" class="input-field w-full" id="cuenta-proveedor-select">
                    <option value="">Sin proveedor</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-cuenta')" class="flex-1 px-4 py-2.5 bg-white/[0.04] text-slate-400 rounded-lg text-sm">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Perfiles -->
<div id="modal-perfiles" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('modal-perfiles')"></div>
    <div class="relative z-10 max-w-lg mx-auto mt-10 bg-[#0a0a0f] border border-white/[0.08] rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-4">Perfiles</h3>
        <div id="perfiles-list" class="space-y-2"></div>
        <button onclick="closeModal('modal-perfiles')" class="mt-4 w-full px-4 py-2.5 bg-white/[0.04] text-slate-400 rounded-lg text-sm">Cerrar</button>
    </div>
</div>

<script>
let cuentasPagina = 1;

document.addEventListener('DOMContentLoaded', async function() {
    await cargarServiciosDropdown();
    await cargarProveedoresDropdown();
    cargarCuentas();
});

async function cargarServiciosDropdown() {
    const res = await apiGet('/api/servicios');
    if (!res.success) return;
    const options = res.data.map(s => `<option value="${s.id}">${s.nombre}</option>`).join('');
    document.getElementById('cuenta-servicio').innerHTML = '<option value="">Todos los servicios</option>' + options;
    document.getElementById('cuenta-servicio-select').innerHTML = '<option value="">Seleccionar...</option>' + options;
}

async function cargarProveedoresDropdown() {
    const res = await apiGet('/api/proveedores');
    if (!res.success) return;
    document.getElementById('cuenta-proveedor-select').innerHTML = '<option value="">Sin proveedor</option>' +
        res.data.map(p => `<option value="${p.id}">${p.nombre}</option>`).join('');
}

async function cargarCuentas(pagina) {
    cuentasPagina = pagina || 1;
    const q = document.getElementById('cuenta-search').value;
    const servicio = document.getElementById('cuenta-servicio').value;
    const estado = document.getElementById('cuenta-estado').value;

    const res = await apiGet(`/api/cuentas?q=${encodeURIComponent(q)}&servicio=${servicio}&estado=${estado}&pagina=${cuentasPagina}`);
    if (!res.success) return;

    document.getElementById('cuentas-table').innerHTML = res.data.cuentas.map((c, i) => {
        const dispColor = parseInt(c.disponibles) >= 3 ? 'text-green-400' : parseInt(c.disponibles) >= 1 ? 'text-yellow-400' : 'text-red-400';
        const vence = new Date(c.fecha_vencimiento);
        const hoy = new Date();
        const diasVence = Math.ceil((vence - hoy) / (1000*60*60*24));
        const venceColor = diasVence < 0 ? 'text-red-400' : diasVence <= 7 ? 'text-orange-400' : '';
        const estadoClass = c.estado === 'activa' ? 'bg-green-500/10 text-green-400' : c.estado === 'vencida' ? 'bg-red-500/10 text-red-400' : 'bg-slate-500/10 text-slate-400';

        return `<tr class="border-b border-white/[0.04]">
            <td class="py-3 text-muted">${res.data.paginacion.offset + i + 1}</td>
            <td class="py-3"><p class="font-medium">${c.correo}</p><p class="text-xs text-muted">${c.password}</p></td>
            <td class="py-3">${c.servicio_nombre || ''}</td>
            <td class="py-3">${c.proveedor_nombre || '-'}</td>
            <td class="py-3"><span class="${dispColor}">${c.vendidos}/${c.disponibles}</span></td>
            <td class="py-3">$${parseFloat(c.costo_usd).toFixed(2)}</td>
            <td class="py-3 ${venceColor}">${c.fecha_vencimiento}</td>
            <td class="py-3"><span class="px-2 py-1 rounded text-xs ${estadoClass}">${c.estado}</span></td>
            <td class="py-3">
                <div class="flex gap-2">
                    <button onclick="verPerfiles(${c.id})" class="text-muted hover:text-white" title="Ver perfiles"><i data-lucide="eye" class="w-4 h-4"></i></button>
                    <button onclick="eliminarCuenta(${c.id})" class="text-muted hover:text-red-400" title="Eliminar"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                </div>
            </td>
        </tr>`;
    }).join('');

    renderPaginacion('cuentas-paginacion', res.data.paginacion, cargarCuentas);
    lucide.createIcons();
}

async function verPerfiles(cuentaId) {
    const res = await apiGet(`/api/cuentas/${cuentaId}/perfiles`);
    if (!res.success) return;

    document.getElementById('perfiles-list').innerHTML = res.data.map(p => `
        <div class="flex items-center justify-between p-3 bg-white/[0.02] rounded-lg">
            <div>
                <span class="font-medium">Perfil ${p.numero_perfil}</span>
                ${p.pin ? `<span class="text-xs text-muted ml-2">PIN: ${p.pin}</span>` : ''}
            </div>
            <div class="flex items-center gap-2">
                ${p.cliente_nombre ? `<span class="text-xs text-accent">${p.cliente_nombre}</span>` : ''}
                <span class="px-2 py-1 rounded text-xs ${p.estado === 'disponible' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400'}">${p.estado}</span>
            </div>
        </div>
    `).join('');
    openModal('modal-perfiles');
}

async function eliminarCuenta(id) {
    if (!confirm('Eliminar esta cuenta y todos sus perfiles?')) return;
    await apiDelete(`/api/cuentas/${id}`);
    cargarCuentas();
}

document.getElementById('form-cuenta').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this));
    await apiPost('/api/cuentas', data);
    closeModal('modal-cuenta');
    this.reset();
    cargarCuentas();
});
</script>
