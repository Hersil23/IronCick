// CSRF Token
function getCSRF() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// API helpers
async function apiGet(url) {
    try {
        const res = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        return await res.json();
    } catch (e) {
        showToast('Error de conexion.', 'error');
        return { success: false };
    }
}

async function apiPost(url, data) {
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCSRF(),
            },
            body: JSON.stringify(data),
        });
        const json = await res.json();
        if (!json.success) showToast(json.message, 'error');
        else if (json.message && json.message !== 'OK') showToast(json.message);
        return json;
    } catch (e) {
        showToast('Error de conexion.', 'error');
        return { success: false };
    }
}

async function apiPut(url, data) {
    try {
        const res = await fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCSRF(),
            },
            body: JSON.stringify(data),
        });
        const json = await res.json();
        if (!json.success) showToast(json.message, 'error');
        else if (json.message && json.message !== 'OK') showToast(json.message);
        return json;
    } catch (e) {
        showToast('Error de conexion.', 'error');
        return { success: false };
    }
}

async function apiDelete(url) {
    try {
        const res = await fetch(url, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCSRF(),
            },
        });
        const json = await res.json();
        if (!json.success) showToast(json.message, 'error');
        else if (json.message && json.message !== 'OK') showToast(json.message);
        return json;
    } catch (e) {
        showToast('Error de conexion.', 'error');
        return { success: false };
    }
}

// Toast notifications
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Modal
function openModal(id) {
    document.getElementById(id)?.classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id)?.classList.add('hidden');
}

// Sidebar toggle
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

// Logout
async function logout() {
    await apiPost('/api/auth/logout', {});
    window.location.href = '/login';
}

// Pagination
function renderPaginacion(containerId, pag, callback) {
    const container = document.getElementById(containerId);
    if (!container || pag.total_paginas <= 1) {
        if (container) container.innerHTML = '';
        return;
    }

    let html = '<div class="flex items-center gap-2 justify-center">';

    if (pag.pagina > 1) {
        html += `<button onclick="${callback.name}(${pag.pagina - 1})" class="px-3 py-1.5 bg-white/[0.04] rounded text-sm text-slate-400 hover:text-white">Anterior</button>`;
    }

    for (let i = Math.max(1, pag.pagina - 2); i <= Math.min(pag.total_paginas, pag.pagina + 2); i++) {
        html += `<button onclick="${callback.name}(${i})" class="px-3 py-1.5 rounded text-sm ${i === pag.pagina ? 'bg-accent text-white' : 'bg-white/[0.04] text-slate-400 hover:text-white'}">${i}</button>`;
    }

    if (pag.pagina < pag.total_paginas) {
        html += `<button onclick="${callback.name}(${pag.pagina + 1})" class="px-3 py-1.5 bg-white/[0.04] rounded text-sm text-slate-400 hover:text-white">Siguiente</button>`;
    }

    html += `<span class="text-xs text-muted ml-2">${pag.total} registros</span></div>`;
    container.innerHTML = html;
}

// Lista de precios
async function openListaPrecios() {
    const res = await apiGet('/api/lista-precios');
    if (!res.success) return;

    const container = document.getElementById('lista-precios-content');
    container.innerHTML = res.data.map(s => `
        <div class="flex items-center justify-between p-3 bg-white/[0.02] rounded-lg">
            <div>
                <p class="font-medium">${s.nombre}</p>
                <p class="text-xs text-muted">${s.duracion} dias</p>
            </div>
            <div class="text-right">
                <p class="font-bold text-accent">$${s.precio_usd.toFixed(2)}</p>
                <p class="text-xs ${s.disponibles > 0 ? 'text-green-400' : 'text-red-400'}">${s.disponibles} disp.</p>
            </div>
        </div>
    `).join('');

    openModal('modal-precios');
}

function copiarListaPrecios() {
    const items = document.querySelectorAll('#lista-precios-content > div');
    let texto = '--- LISTA DE PRECIOS ---\n\n';
    items.forEach(item => {
        const nombre = item.querySelector('.font-medium').textContent;
        const precio = item.querySelector('.text-accent').textContent;
        const disp = item.querySelector('.text-xs:last-child').textContent;
        texto += `${nombre}\nPrecio: ${precio}\n${disp}\n\n`;
    });

    navigator.clipboard.writeText(texto).then(() => {
        showToast('Lista copiada al portapapeles.');
    });
}

function compartirImagen() {
    // Create canvas for price list image
    const canvas = document.createElement('canvas');
    canvas.width = 600;
    canvas.height = 800;
    const ctx = canvas.getContext('2d');

    ctx.fillStyle = '#060608';
    ctx.fillRect(0, 0, 600, 800);

    ctx.fillStyle = '#ea580c';
    ctx.font = 'bold 28px sans-serif';
    ctx.fillText('Lista de Precios', 30, 50);

    ctx.fillStyle = '#f1f5f9';
    ctx.font = '16px sans-serif';

    const items = document.querySelectorAll('#lista-precios-content > div');
    let y = 100;
    items.forEach(item => {
        const nombre = item.querySelector('.font-medium').textContent;
        const precio = item.querySelector('.text-accent').textContent;

        ctx.fillStyle = 'rgba(255,255,255,0.04)';
        ctx.fillRect(20, y - 20, 560, 50);

        ctx.fillStyle = '#f1f5f9';
        ctx.fillText(nombre, 30, y + 5);

        ctx.fillStyle = '#ea580c';
        ctx.textAlign = 'right';
        ctx.fillText(precio, 570, y + 5);
        ctx.textAlign = 'left';

        y += 60;
    });

    canvas.toBlob(blob => {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'lista-precios.png';
        a.click();
        URL.revokeObjectURL(url);
        showToast('Imagen descargada.');
    });
}

// Debounce search
let searchTimeout;
function debounceSearch(fn, delay) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => fn(), delay);
}

// Register service worker
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
}
