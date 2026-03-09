<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold font-['Syne']">Servicios</h2>
    <button onclick="openModal('modal-servicio')" class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Nuevo Servicio
    </button>
</div>

<div class="glass rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-muted border-b border-white/[0.08]">
                    <th class="pb-3">Servicio</th>
                    <th class="pb-3">Perfiles/Cuenta</th>
                    <th class="pb-3">Cuentas</th>
                    <th class="pb-3">Disponibles</th>
                    <th class="pb-3">Vendidos</th>
                    <th class="pb-3">Estado</th>
                    <th class="pb-3">Acciones</th>
                </tr>
            </thead>
            <tbody id="servicios-table"></tbody>
        </table>
    </div>
</div>

<!-- Modal Crear/Editar Servicio -->
<div id="modal-servicio" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('modal-servicio')"></div>
    <div class="relative z-10 max-w-lg mx-auto mt-10 bg-[#0a0a0f] border border-white/[0.08] rounded-2xl p-6 max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold font-['Syne'] mb-6" id="modal-servicio-title">Nuevo Servicio</h3>
        <form id="form-servicio" class="space-y-4">
            <input type="hidden" name="id" id="servicio-id">
            <div>
                <label class="block text-sm text-slate-400 mb-1">Nombre</label>
                <input type="text" name="nombre" required class="input-field w-full" placeholder="Netflix, Spotify...">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Precio USD</label>
                    <input type="number" step="0.01" name="precio_usd" required class="input-field w-full" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Duracion (dias)</label>
                    <input type="number" name="duracion_dias" required class="input-field w-full" placeholder="30">
                </div>
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Perfiles por cuenta</label>
                <input type="number" name="perfiles_por_cuenta" required class="input-field w-full" placeholder="5" min="1">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Descripcion corta</label>
                <textarea name="descripcion" class="input-field w-full" rows="2" placeholder="Descripcion para la tienda..."></textarea>
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Correo IMAP (opcional)</label>
                <input type="email" name="imap_correo" class="input-field w-full" placeholder="verificacion@gmail.com">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Contrasena IMAP (App Password)</label>
                <input type="password" name="imap_password" class="input-field w-full" placeholder="Contrasena de aplicacion">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-servicio')" class="flex-1 px-4 py-2.5 bg-white/[0.04] text-slate-400 rounded-lg text-sm">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', cargarServicios);

async function cargarServicios() {
    const res = await apiGet('/api/servicios');
    if (!res.success) return;

    document.getElementById('servicios-table').innerHTML = res.data.map(s => `
        <tr class="border-b border-white/[0.04]">
            <td class="py-3 font-medium">${s.nombre}</td>
            <td class="py-3"><span class="px-2 py-1 bg-accent/10 text-accent rounded text-xs">${s.perfiles_por_cuenta}</span></td>
            <td class="py-3">${s.total_cuentas}</td>
            <td class="py-3 ${parseInt(s.disponibles) <= 2 ? 'text-red-400' : 'text-green-400'}">${s.disponibles}</td>
            <td class="py-3">${s.vendidos}</td>
            <td class="py-3"><span class="px-2 py-1 rounded text-xs ${s.estado === 'activo' ? 'bg-green-500/10 text-green-400' : 'bg-slate-500/10 text-slate-400'}">${s.estado}</span></td>
            <td class="py-3">
                <div class="flex gap-2">
                    <button onclick='editarServicio(${JSON.stringify(s)})' class="text-muted hover:text-white"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                    <button onclick="toggleServicio(${s.id})" class="text-muted hover:text-yellow-400"><i data-lucide="power" class="w-4 h-4"></i></button>
                </div>
            </td>
        </tr>
    `).join('');
    lucide.createIcons();
}

function editarServicio(s) {
    document.getElementById('modal-servicio-title').textContent = 'Editar Servicio';
    document.getElementById('servicio-id').value = s.id;
    const form = document.getElementById('form-servicio');
    form.nombre.value = s.nombre;
    form.precio_usd.value = s.precio_usd;
    form.duracion_dias.value = s.duracion_dias;
    form.perfiles_por_cuenta.value = s.perfiles_por_cuenta;
    form.descripcion.value = s.descripcion || '';
    form.imap_correo.value = s.imap_correo || '';
    openModal('modal-servicio');
}

async function toggleServicio(id) {
    await apiDelete(`/api/servicios/${id}`);
    cargarServicios();
}

document.getElementById('form-servicio').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    const data = Object.fromEntries(fd);
    const id = data.id;
    delete data.id;

    if (id) {
        await apiPut(`/api/servicios/${id}`, data);
    } else {
        await apiPost('/api/servicios', data);
    }

    closeModal('modal-servicio');
    this.reset();
    cargarServicios();
});
</script>
