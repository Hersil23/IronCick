<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold font-['Syne']">Clientes</h2>
    <button onclick="openModal('modal-cliente')" class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Nuevo Cliente
    </button>
</div>

<!-- Busqueda -->
<div class="flex flex-wrap gap-3 mb-6">
    <input type="text" id="cliente-search" placeholder="Buscar por nombre, telefono o email..."
           class="input-field flex-1 min-w-[200px]" oninput="debounceSearch(cargarClientes, 300)">
    <select id="cliente-estado" class="input-field" onchange="cargarClientes()">
        <option value="">Todos</option>
        <option value="activo">Activo</option>
        <option value="vencido">Vencido</option>
        <option value="suspendido">Suspendido</option>
    </select>
</div>

<div class="glass rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-muted border-b border-white/[0.08]">
                    <th class="pb-3">#</th>
                    <th class="pb-3">Cliente</th>
                    <th class="pb-3">Telefono</th>
                    <th class="pb-3">Email</th>
                    <th class="pb-3">Servicios</th>
                    <th class="pb-3">Saldo</th>
                    <th class="pb-3">Ultimo pago</th>
                    <th class="pb-3">Estado</th>
                    <th class="pb-3">Acciones</th>
                </tr>
            </thead>
            <tbody id="clientes-table"></tbody>
        </table>
    </div>
    <div id="clientes-paginacion" class="mt-4"></div>
</div>

<!-- Modal Cliente -->
<div id="modal-cliente" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('modal-cliente')"></div>
    <div class="relative z-10 max-w-lg mx-auto mt-20 bg-[#0a0a0f] border border-white/[0.08] rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-6" id="modal-cliente-title">Nuevo Cliente</h3>
        <form id="form-cliente" class="space-y-4">
            <input type="hidden" name="id" id="cliente-id">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Nombre</label>
                    <input type="text" name="nombre" required class="input-field w-full">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Apellido</label>
                    <input type="text" name="apellido" class="input-field w-full">
                </div>
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Telefono WhatsApp</label>
                <input type="text" name="telefono" required class="input-field w-full" placeholder="+58 412 1234567">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Email (opcional)</label>
                <input type="email" name="email" class="input-field w-full">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-cliente')" class="flex-1 px-4 py-2.5 bg-white/[0.04] text-slate-400 rounded-lg text-sm">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
let clientesPagina = 1;
document.addEventListener('DOMContentLoaded', cargarClientes);

async function cargarClientes(pagina) {
    clientesPagina = pagina || 1;
    const q = document.getElementById('cliente-search').value;
    const estado = document.getElementById('cliente-estado').value;

    const res = await apiGet(`/api/clientes?q=${encodeURIComponent(q)}&estado=${estado}&pagina=${clientesPagina}`);
    if (!res.success) return;

    const estadoClasses = {
        activo: 'bg-green-500/10 text-green-400',
        vencido: 'bg-orange-500/10 text-orange-400',
        suspendido: 'bg-slate-500/10 text-slate-400',
    };

    document.getElementById('clientes-table').innerHTML = res.data.clientes.map((c, i) => `
        <tr class="border-b border-white/[0.04]">
            <td class="py-3 text-muted">${res.data.paginacion.offset + i + 1}</td>
            <td class="py-3 font-medium">${c.nombre} ${c.apellido || ''}</td>
            <td class="py-3">${c.telefono ? `<a href="https://wa.me/${c.telefono.replace(/\\D/g,'')}" target="_blank" class="text-green-400 hover:underline">${c.telefono}</a>` : '-'}</td>
            <td class="py-3 text-muted">${c.email || '-'}</td>
            <td class="py-3"><a href="/ventas?cliente=${c.id}" class="text-accent hover:underline">${c.servicios_activos || 0}</a></td>
            <td class="py-3">$${parseFloat(c.creditos || 0).toFixed(2)}</td>
            <td class="py-3 text-muted text-xs">${c.ultimo_pago || '-'}</td>
            <td class="py-3"><span class="px-2 py-1 rounded text-xs ${estadoClasses[c.estado] || ''}">${c.estado}</span></td>
            <td class="py-3">
                <div class="flex gap-2">
                    <button onclick='editarCliente(${JSON.stringify(c)})' class="text-muted hover:text-white"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                    <button onclick="cambiarEstadoCliente(${c.id}, '${c.estado === 'activo' ? 'suspendido' : 'activo'}')" class="text-muted hover:text-yellow-400"><i data-lucide="power" class="w-4 h-4"></i></button>
                    <button onclick="eliminarCliente(${c.id})" class="text-muted hover:text-red-400"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                </div>
            </td>
        </tr>
    `).join('');

    renderPaginacion('clientes-paginacion', res.data.paginacion, cargarClientes);
    lucide.createIcons();
}

function editarCliente(c) {
    document.getElementById('modal-cliente-title').textContent = 'Editar Cliente';
    document.getElementById('cliente-id').value = c.id;
    const form = document.getElementById('form-cliente');
    form.nombre.value = c.nombre;
    form.apellido.value = c.apellido || '';
    form.telefono.value = c.telefono || '';
    form.email.value = c.email || '';
    openModal('modal-cliente');
}

async function cambiarEstadoCliente(id, estado) {
    await apiPut(`/api/clientes/${id}/estado`, { estado });
    cargarClientes();
}

async function eliminarCliente(id) {
    if (!confirm('Eliminar este cliente?')) return;
    await apiPut(`/api/clientes/${id}/estado`, { estado: 'eliminado' });
    cargarClientes();
}

document.getElementById('form-cliente').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    const data = Object.fromEntries(fd);
    const id = data.id;
    delete data.id;

    if (id) {
        await apiPut(`/api/clientes/${id}`, data);
    } else {
        await apiPost('/api/clientes', data);
    }
    closeModal('modal-cliente');
    this.reset();
    cargarClientes();
});
</script>
