<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion - IronClick</title>
    <link rel="icon" type="image/x-icon" href="/assets/img/logo/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Syne:wght@700&family=Orbitron:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="bg-[#060608] text-slate-200 font-['DM_Sans'] min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <img src="/assets/img/logo/logo-full.svg" alt="IronClick" class="h-16 mx-auto mb-4">
        </div>

        <div class="bg-white/[0.04] border border-white/[0.08] rounded-2xl p-8 backdrop-blur-xl">
            <h2 class="text-xl font-bold font-['Syne'] mb-6 text-center">Iniciar Sesion</h2>

            <div id="login-error" class="hidden mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-lg text-red-400 text-sm"></div>

            <form id="login-form" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= CSRF::generate() ?>">

                <div>
                    <label class="block text-sm text-slate-400 mb-1.5">Email</label>
                    <input type="email" name="email" required autocomplete="email"
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-accent transition-colors"
                           placeholder="tu@email.com">
                </div>

                <div>
                    <label class="block text-sm text-slate-400 mb-1.5">Contrasena</label>
                    <input type="password" name="password" required autocomplete="current-password"
                           class="w-full px-4 py-3 bg-white/[0.04] border border-white/[0.08] rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-accent transition-colors"
                           placeholder="Tu contrasena">
                </div>

                <button type="submit" id="login-btn"
                        class="w-full py-3 bg-accent hover:bg-[#f97316] text-white font-semibold rounded-lg transition-colors">
                    Entrar
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">Powered by IronClick</p>
    </div>

    <script>
    document.getElementById('login-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('login-btn');
        const errorDiv = document.getElementById('login-error');
        errorDiv.classList.add('hidden');
        btn.disabled = true;
        btn.textContent = 'Entrando...';

        try {
            const formData = new FormData(this);
            const params = new URLSearchParams(window.location.search);
            const panelParam = params.get('panel');
            const loginUrl = '/api/auth/login' + (panelParam ? '?panel=' + encodeURIComponent(panelParam) : '');
            const res = await fetch(loginUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': formData.get('csrf_token'),
                },
                body: JSON.stringify({
                    email: formData.get('email'),
                    password: formData.get('password'),
                }),
            });

            const data = await res.json();

            if (data.success) {
                const redirect = data.data.redirect;
                window.location.href = panelParam ? redirect + (redirect.includes('?') ? '&' : '?') + 'panel=' + encodeURIComponent(panelParam) : redirect;
            } else {
                errorDiv.textContent = data.message;
                errorDiv.classList.remove('hidden');
            }
        } catch (err) {
            errorDiv.textContent = 'Error de conexion. Intenta de nuevo.';
            errorDiv.classList.remove('hidden');
        }

        btn.disabled = false;
        btn.textContent = 'Entrar';
    });
    </script>
</body>
</html>
