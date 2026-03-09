<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold font-['Syne']">Distribuidores</h2>
    <button onclick="openModal('modal-distribuidor')" class="flex items-center gap-2 px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Nuevo Distribuidor
    </button>
</div>

<div class="glass rounded-2xl p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-muted border-b border-white/[0.08]">
                    <th class="pb-3">Nombre</th>
                    <th class="pb-3">Telefono</th>
                    <th class="pb-3">Vendedores</th>
                    <th class="pb-3">Clientes</th>
                    <th class="pb-3">Ventas del mes</th>
                    <th class="pb-3">Creditos</th>
                    <th class="pb-3">Estado</th>
                    <th class="pb-3">Acciones</th>
                </tr>
            </thead>
            <tbody id="distribuidores-table">
                <tr><td colspan="8" class="py-8 text-center text-muted">Cargando...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="modal-distribuidor" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="closeModal('modal-distribuidor')"></div>
    <div class="relative z-10 max-w-lg mx-auto mt-20 bg-[#0a0a0f] border border-white/[0.08] rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-6">Nuevo Distribuidor</h3>
        <form id="form-distribuidor" class="space-y-4">
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
                <button type="button" onclick="closeModal('modal-distribuidor')" class="flex-1 px-4 py-2.5 bg-white/[0.04] text-slate-400 rounded-lg text-sm">Cancelar</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Crear</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // TODO: cargar distribuidores
});
</script>
