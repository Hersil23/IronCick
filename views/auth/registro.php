<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Panel - IronClick</title>
    <link rel="icon" type="image/x-icon" href="/assets/img/logo/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: { extend: { colors: { accent: '#ea580c', 'accent-hover': '#f97316' } } }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@700&family=Orbitron:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-[#060608] text-slate-200 font-['DM_Sans'] min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <a href="/">
                <img src="/assets/img/logo/logo-full.svg" alt="IronClick" class="h-16 mx-auto mb-4">
            </a>
        </div>

        <div class="bg-white/[0.04] border border-white/[0.08] rounded-2xl p-8 backdrop-blur-xl">
            <h2 class="text-xl font-bold font-['Syne'] mb-6 text-center">Crear tu Panel</h2>

            <div id="registro-error" class="hidden mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-sm"></div>
            <div id="registro-success" class="hidden mb-4 p-3 bg-green-500/10 border border-green-500/20 rounded-lg text-green-400 text-sm"></div>

            <form id="registro-form" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= CSRF::generate() ?>">
                <input type="hidden" name="plan" value="<?= htmlspecialchars($_GET['plan'] ?? 'estandar') ?>">

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm text-slate-400 mb-1.5">Nombre</label>
                        <input type="text" name="nombre_contacto" required
                               class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-accent transition-colors"
                               placeholder="Tu nombre">
                    </div>
                    <div>
                        <label class="block text-sm text-slate-400 mb-1.5">Apellido</label>
                        <input type="text" name="apellido_contacto" required
                               class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-accent transition-colors"
                               placeholder="Tu apellido">
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-slate-400 mb-1.5">Nombre del negocio</label>
                    <input type="text" name="nombre" required
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-accent transition-colors"
                           placeholder="Ej: Mi Streaming">
                </div>

                <div>
                    <label class="block text-sm text-slate-400 mb-1.5">Subdominio</label>
                    <div class="flex items-center gap-0">
                        <input type="text" name="subdominio" required pattern="[a-z0-9\-]+" minlength="3" maxlength="30"
                               class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-l-lg text-white placeholder-slate-500 focus:outline-none focus:border-accent transition-colors"
                               placeholder="mi-negocio" oninput="this.value = this.value.toLowerCase().replace(/[^a-z0-9\-]/g, '')">
                        <span class="px-3 py-3 bg-white/[0.02] border border-l-0 border-white/[0.08] rounded-r-lg text-slate-500 text-sm whitespace-nowrap">.ironclick.app</span>
                    </div>
                    <p id="subdominio-status" class="text-xs mt-1 hidden"></p>
                </div>

                <div>
                    <label class="block text-sm text-slate-400 mb-1.5">Telefono / WhatsApp</label>
                    <input type="tel" name="telefono" required
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-accent transition-colors"
                           placeholder="Ej: +58 412 1234567">
                </div>

                <div>
                    <label class="block text-sm text-slate-400 mb-1.5">Email</label>
                    <input type="email" name="email" required autocomplete="email"
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-accent transition-colors"
                           placeholder="tu@email.com">
                </div>

                <div>
                    <label class="block text-sm text-slate-400 mb-1.5">Contrasena</label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-accent transition-colors"
                           placeholder="Minimo 6 caracteres">
                </div>

                <div>
                    <label class="block text-sm text-slate-400 mb-1.5">Confirmar contrasena</label>
                    <input type="password" name="password_confirm" required minlength="6"
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-accent transition-colors"
                           placeholder="Repite tu contrasena">
                </div>

                <div class="pt-2">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-accent/10 border border-accent/20 text-accent">
                        Plan: <?= htmlspecialchars(ucfirst($_GET['plan'] ?? 'estandar')) ?>
                    </span>
                    <span class="text-xs text-slate-500 ml-2">Trial de <?= TRIAL_DAYS ?> dias incluido</span>
                </div>

                <button type="submit" id="registro-btn"
                        class="w-full py-3 bg-accent hover:bg-accent-hover text-white font-semibold rounded-lg transition-colors mt-2">
                    Crear Panel
                </button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-6">
                Ya tienes cuenta? <a href="/login" class="text-accent hover:underline">Ingresar</a>
            </p>
        </div>
    </div>

    <script>
    // Check subdominio availability
    let subdominioTimer;
    document.querySelector('input[name="subdominio"]').addEventListener('input', function() {
        clearTimeout(subdominioTimer);
        const status = document.getElementById('subdominio-status');
        const val = this.value.trim();
        if (val.length < 3) {
            status.classList.add('hidden');
            return;
        }
        subdominioTimer = setTimeout(async () => {
            try {
                const res = await fetch('/api/auth/check-subdominio?subdominio=' + encodeURIComponent(val));
                const data = await res.json();
                status.classList.remove('hidden');
                if (data.data.available) {
                    status.textContent = val + '.ironclick.app esta disponible';
                    status.className = 'text-xs mt-1 text-green-400';
                } else {
                    status.textContent = 'Este subdominio ya esta en uso';
                    status.className = 'text-xs mt-1 text-red-400';
                }
            } catch(e) {}
        }, 500);
    });

    document.getElementById('registro-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('registro-btn');
        const errorDiv = document.getElementById('registro-error');
        const successDiv = document.getElementById('registro-success');
        errorDiv.classList.add('hidden');
        successDiv.classList.add('hidden');

        const formData = new FormData(this);
        if (formData.get('password') !== formData.get('password_confirm')) {
            errorDiv.textContent = 'Las contrasenas no coinciden.';
            errorDiv.classList.remove('hidden');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Creando...';

        try {
            const res = await fetch('/api/auth/registro', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': formData.get('csrf_token'),
                },
                body: JSON.stringify({
                    nombre_contacto: formData.get('nombre_contacto'),
                    apellido_contacto: formData.get('apellido_contacto'),
                    telefono: formData.get('telefono'),
                    nombre: formData.get('nombre'),
                    subdominio: formData.get('subdominio'),
                    email: formData.get('email'),
                    password: formData.get('password'),
                    plan: formData.get('plan'),
                }),
            });

            const data = await res.json();

            if (data.success) {
                successDiv.textContent = 'Panel creado exitosamente. Redirigiendo...';
                successDiv.classList.remove('hidden');
                setTimeout(() => {
                    window.location.href = data.data.redirect;
                }, 1500);
            } else {
                errorDiv.textContent = data.message;
                errorDiv.classList.remove('hidden');
            }
        } catch (err) {
            errorDiv.textContent = 'Error de conexion. Intenta de nuevo.';
            errorDiv.classList.remove('hidden');
        }

        btn.disabled = false;
        btn.textContent = 'Crear Panel';
    });
    </script>
</body>
</html>
