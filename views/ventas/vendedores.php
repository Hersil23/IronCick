<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold font-['Syne']">Vendedores</h2>
    <button onclick="openModal('modal-vendedor')" class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Nuevo Vendedor
    </button>
</div>

<div class="glass rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-muted border-b border-white/[0.08]">
                    <th class="pb-3">Nombre</th>
                    <th class="pb-3">Telefono</th>
                    <th class="pb-3">Clientes</th>
                    <th class="pb-3">Ventas del mes</th>
                    <th class="pb-3">Creditos</th>
                    <th class="pb-3">Estado</th>
                    <th class="pb-3">Acciones</th>
                </tr>
            </thead>
            <tbody id="vendedores-table"></tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="modal-vendedor" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('modal-vendedor')"></div>
    <div class="relative z-10 max-w-lg mx-auto mt-20 bg-[#0a0a0f] border border-white/[0.08] rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-6">Nuevo Vendedor</h3>
        <form id="form-vendedor" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-400 mb-1">Nombre</label>
                <input type="text" name="nombre" required class="input-field w-full">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Email</label>
                <input type="email" name="email" required class="input-field w-full">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Contrasena</label>
                <input type="password" name="password" required class="input-field w-full" minlength="6">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Telefono</label>
                <input type="text" name="telefono" class="input-field w-full">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('modal-vendedor')" class="flex-1 px-4 py-2.5 bg-white/[0.04] text-slate-400 rounded-lg text-sm">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Crear</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', cargarVendedores);

async function cargarVendedores() {
    // Uses usuarios endpoint filtered by role
    const res = await apiGet('/api/clientes'); // Will be replaced with proper vendedores endpoint
    // For now, load from a custom call
    const tbody = document.getElementById('vendedores-table');
    tbody.innerHTML = '<tr><td colspan="7" class="py-8 text-center text-muted">Cargando vendedores...</td></tr>';

    // TODO: implement vendedores list API
}

document.getElementById('form-vendedor').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this));
    data.rol = 'vendedor';
    // TODO: POST to create vendedor
    closeModal('modal-vendedor');
    this.reset();
    cargarVendedores();
});
</script>
