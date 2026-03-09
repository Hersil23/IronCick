<?php ob_start(); ?>

<h1 class="text-3xl font-bold font-['Syne'] mb-8">Mi Cuenta</h1>

<div class="max-w-lg mx-auto">
    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente'): ?>
    <!-- Cliente logueado -->
    <div class="space-y-6">
        <div class="bg-white/[0.04] border border-white/[0.08] rounded-2xl p-6">
            <h3 class="font-semibold mb-3">Billetera</h3>
            <p class="text-3xl font-bold" style="color:var(--tienda-primario)" id="mi-saldo">$0.00</p>
        </div>

        <div class="bg-white/[0.04] border border-white/[0.08] rounded-2xl p-6">
            <h3 class="font-semibold mb-4">Mis Servicios</h3>
            <div id="mis-servicios" class="space-y-3">
                <p class="text-muted text-sm">Cargando...</p>
            </div>
        </div>

        <button onclick="window.location.href='/logout'" class="w-full py-3 bg-white/[0.04] text-slate-400 rounded-lg hover:bg-white/[0.07] transition-colors">
            Cerrar Sesion
        </button>
    </div>
    <?php else: ?>
    <!-- Login -->
    <div class="bg-white/[0.04] border border-white/[0.08] rounded-2xl p-6">
        <h3 class="font-semibold mb-4 text-center">Iniciar Sesion</h3>
        <div id="mi-cuenta-error" class="hidden mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-sm"></div>
        <form id="form-mi-cuenta-login" class="space-y-4">
            <div>
                <label class="block text-sm text-slate-400 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none" placeholder="tu@email.com">
            </div>
            <div>
                <label class="block text-sm text-slate-400 mb-1">Contrasena</label>
                <input type="password" name="password" required class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none">
            </div>
            <button type="submit" class="w-full py-3 rounded-lg text-white font-semibold" style="background:var(--tienda-primario)">Entrar</button>
        </form>
    </div>

    <!-- Seguimiento rapido -->
    <div class="mt-8 bg-white/[0.04] border border-white/[0.08] rounded-2xl p-6">
        <h3 class="font-semibold mb-4 text-center">Seguimiento de Pedido</h3>
        <form onsubmit="buscarOrden(event)" class="flex gap-3">
            <input type="text" id="orden-input" placeholder="IC-XXXXXXXX" class="flex-1 px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none">
            <button type="submit" class="px-6 py-3 rounded-lg text-white font-medium" style="background:var(--tienda-primario)">Buscar</button>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
function buscarOrden(e) {
    e.preventDefault();
    const orden = document.getElementById('orden-input').value.trim();
    if (orden) window.location.href = '/pedido/' + encodeURIComponent(orden);
}

<?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'cliente'): ?>
// TODO: Cargar datos del cliente logueado via API
<?php else: ?>
document.getElementById('form-mi-cuenta-login')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    const errorDiv = document.getElementById('mi-cuenta-error');
    errorDiv.classList.add('hidden');

    try {
        const res = await fetch('/api/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: fd.get('email'), password: fd.get('password') }),
        });
        const data = await res.json();
        if (data.success) {
            window.location.reload();
        } else {
            errorDiv.textContent = data.message;
            errorDiv.classList.remove('hidden');
        }
    } catch (err) {
        errorDiv.textContent = 'Error de conexion.';
        errorDiv.classList.remove('hidden');
    }
});
<?php endif; ?>
</script>

<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/tienda.php'; ?>
