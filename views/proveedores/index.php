<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold font-['Syne']">Proveedores</h2>
    <button onclick="openModal('modal-proveedor')" class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Nuevo Proveedor
    </button>
</div>

<div class="glass rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-muted border-b border-white/[0.08]">
                    <th class="pb-3">Nombre</th>
                    <th class="pb-3">Contacto</th>
                    <th class="pb-3">Telefono</th>
                    <th class="pb-3">Cuentas</th>
                    <th class="pb-3">Notas</th>
                    <th class="pb-3">Estado</th>
                    <th class="pb-3">Acciones</th>
                </tr>
            </thead>
            <tbody id="proveedores-table"></tbody>
        </table>
    </div>
</div>

<!-- Modal Proveedor -->
<div id="modal-proveedor" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('modal-proveedor')"></div>
    <div class="relative z-10 max-w-lg mx-auto mt-20 bg-[#0a0a0f] border border-white/[0.08] rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-6" id="modal-proveedor-title">Nuevo Proveedor</h3>
        <form id="form-proveedor" class="space-y-4">
            <input type="hidden" name="id" id="proveedor-id">
            <div>
                <label class="block text-sm text-slate-400 mb-1">Nombre</label>
                <input type="text" name="nombre" required class="input-field w-full">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Contacto (persona)</label>
                <input type="text" name="contacto" class="input-field w-full">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Telefono WhatsApp</label>
                <input type="text" name="telefono" class="input-field w-full" placeholder="+58 412 1234567">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Notas</label>
                <textarea name="notas" class="input-field w-full" rows="2"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-proveedor')" class="flex-1 px-4 py-2.5 bg-white/[0.04] text-slate-400 rounded-lg text-sm">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', cargarProveedores);

async function cargarProveedores() {
    const res = await apiGet('/api/proveedores');
    if (!res.success) return;

    document.getElementById('proveedores-table').innerHTML = res.data.map(p => `
        <tr class="border-b border-white/[0.04]">
            <td class="py-3 font-medium">${p.nombre}</td>
            <td class="py-3">${p.contacto || '-'}</td>
            <td class="py-3">${p.telefono ? `<a href="https://wa.me/${p.telefono.replace(/\\D/g,'')}" target="_blank" class="text-green-400 hover:underline">${p.telefono}</a>` : '-'}</td>
            <td class="py-3"><a href="/cuentas?proveedor=${p.id}" class="text-accent hover:underline">${p.total_cuentas}</a></td>
            <td class="py-3 text-muted text-xs max-w-[200px] truncate">${p.notas || ''}</td>
            <td class="py-3"><span class="px-2 py-1 rounded text-xs ${p.estado === 'activo' ? 'bg-green-500/10 text-green-400' : 'bg-slate-500/10 text-slate-400'}">${p.estado}</span></td>
            <td class="py-3">
                <button onclick='editarProveedor(${JSON.stringify(p)})' class="text-muted hover:text-white"><i data-lucide="pencil" class="w-4 h-4"></i></button>
            </td>
        </tr>
    `).join('');
    lucide.createIcons();
}

function editarProveedor(p) {
    document.getElementById('modal-proveedor-title').textContent = 'Editar Proveedor';
    document.getElementById('proveedor-id').value = p.id;
    const form = document.getElementById('form-proveedor');
    form.nombre.value = p.nombre;
    form.contacto.value = p.contacto || '';
    form.telefono.value = p.telefono || '';
    form.notas.value = p.notas || '';
    openModal('modal-proveedor');
}

document.getElementById('form-proveedor').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    const data = Object.fromEntries(fd);
    const id = data.id;
    delete data.id;

    if (id) {
        await apiPut(`/api/proveedores/${id}`, data);
    } else {
        await apiPost('/api/proveedores', data);
    }
    closeModal('modal-proveedor');
    this.reset();
    cargarProveedores();
});
</script>
