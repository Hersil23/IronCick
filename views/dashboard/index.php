<!-- Banner de bienvenida -->
<div class="glass rounded-2xl p-6 mb-8">
    <h1 class="text-2xl font-bold font-['Syne']">
        <span id="saludo"><?= saludo() ?></span>, <?= htmlspecialchars(Auth::nombre()) ?>
    </h1>
    <p class="text-muted mt-1">
        <span id="fecha-completa"><?= fechaCompleta() ?></span> -
        <span id="hora-actual"></span>
    </p>
</div>

<!-- Filtro de periodo -->
<div class="flex items-center gap-2 mb-6 flex-wrap">
    <span class="text-sm text-muted">Periodo:</span>
    <?php foreach (['hoy' => 'Hoy', 'semana' => 'Esta semana', 'mes' => 'Este mes', 'ano' => 'Este ano'] as $key => $label): ?>
    <button onclick="cambiarPeriodo('<?= $key ?>')" data-periodo="<?= $key ?>"
            class="periodo-btn px-3 py-1.5 rounded-lg text-sm transition-colors <?= $key === 'mes' ? 'bg-accent text-white' : 'bg-white/[0.04] text-slate-400 hover:bg-white/[0.07]' ?>">
        <?= $label ?>
    </button>
    <?php endforeach; ?>
</div>

<!-- Metricas fila 1 -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="glass rounded-xl p-5">
        <p class="text-sm text-muted mb-1">Ingresos</p>
        <p class="text-2xl font-bold text-green-400" id="m-ingresos">$0.00</p>
    </div>
    <div class="glass rounded-xl p-5">
        <p class="text-sm text-muted mb-1">Costos</p>
        <p class="text-2xl font-bold text-red-400" id="m-costos">$0.00</p>
    </div>
    <div class="glass rounded-xl p-5">
        <p class="text-sm text-muted mb-1">Utilidad</p>
        <p class="text-2xl font-bold text-accent" id="m-utilidad">$0.00</p>
    </div>
    <div class="glass rounded-xl p-5">
        <p class="text-sm text-muted mb-1">Inversion Activa</p>
        <p class="text-2xl font-bold text-blue-400" id="m-inversion">$0.00</p>
    </div>
</div>

<!-- Metricas fila 2 -->
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    <div class="glass rounded-xl p-4">
        <p class="text-xs text-muted">Ventas Nuevas</p>
        <p class="text-lg font-bold" id="m-ventas-nuevas">0</p>
        <p class="text-xs text-muted" id="m-ventas-nuevas-monto">$0.00</p>
    </div>
    <div class="glass rounded-xl p-4">
        <p class="text-xs text-muted">Renovaciones</p>
        <p class="text-lg font-bold" id="m-renovaciones">0</p>
        <p class="text-xs text-muted" id="m-renovaciones-monto">$0.00</p>
    </div>
    <div class="glass rounded-xl p-4">
        <p class="text-xs text-muted">Clientes Activos</p>
        <p class="text-lg font-bold" id="m-clientes">0</p>
    </div>
    <div class="glass rounded-xl p-4">
        <p class="text-xs text-muted">Cuentas Activas</p>
        <p class="text-lg font-bold" id="m-cuentas">0</p>
    </div>
    <div class="glass rounded-xl p-4">
        <p class="text-xs text-muted">Perfiles</p>
        <p class="text-lg font-bold"><span id="m-perfiles-v">0</span> / <span id="m-perfiles-d">0</span></p>
    </div>
    <div class="glass rounded-xl p-4">
        <p class="text-xs text-muted">Retencion</p>
        <p class="text-lg font-bold text-accent" id="m-retencion">0%</p>
    </div>
</div>

<!-- Stock de Cuentas -->
<div class="glass rounded-2xl p-6 mb-8">
    <h3 class="text-lg font-semibold font-['Syne'] mb-4">Stock de Cuentas</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-muted border-b border-white/[0.08]">
                    <th class="pb-3">Servicio</th>
                    <th class="pb-3">Cuentas</th>
                    <th class="pb-3">Perfiles</th>
                    <th class="pb-3">Vendidos</th>
                    <th class="pb-3">Disponibles</th>
                    <th class="pb-3 w-40">Ocupacion</th>
                    <th class="pb-3">Costo</th>
                    <th class="pb-3">Ingreso Pot.</th>
                    <th class="pb-3">Ganancia</th>
                </tr>
            </thead>
            <tbody id="stock-table"></tbody>
        </table>
    </div>
</div>

<!-- Graficas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-4">Ventas por Servicio</h3>
        <canvas id="chart-ventas-servicio" height="250"></canvas>
    </div>
    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-4">Distribucion de Ingresos</h3>
        <canvas id="chart-ingresos" height="250"></canvas>
    </div>
</div>

<!-- Ventas por Cobrar + Cuentas por Vencer -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-4">Ventas por Cobrar (3 dias)</h3>
        <div id="ventas-cobrar" class="space-y-3"></div>
    </div>
    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-4">Cuentas por Vencer (7 dias)</h3>
        <div id="cuentas-vencer" class="space-y-3"></div>
    </div>
</div>

<!-- Ultimos Pagos -->
<div class="glass rounded-2xl p-6">
    <h3 class="text-lg font-semibold font-['Syne'] mb-4">Ultimos Pagos</h3>
    <div id="ultimos-pagos" class="space-y-3"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reloj en tiempo real
    function updateClock() {
        const now = new Date();
        document.getElementById('hora-actual').textContent = now.toLocaleTimeString('es-ES');
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Cargar datos
    cargarDashboard('mes');
});

let currentPeriodo = 'mes';

function cambiarPeriodo(periodo) {
    currentPeriodo = periodo;
    document.querySelectorAll('.periodo-btn').forEach(btn => {
        btn.classList.remove('bg-accent', 'text-white');
        btn.classList.add('bg-white/[0.04]', 'text-slate-400');
    });
    document.querySelector(`[data-periodo="${periodo}"]`).classList.add('bg-accent', 'text-white');
    document.querySelector(`[data-periodo="${periodo}"]`).classList.remove('bg-white/[0.04]', 'text-slate-400');
    cargarDashboard(periodo);
}

async function cargarDashboard(periodo) {
    // Metricas
    const metricas = await apiGet(`/api/dashboard/metricas?periodo=${periodo}`);
    if (metricas.success) {
        const d = metricas.data;
        document.getElementById('m-ingresos').textContent = '$' + d.ingresos.toFixed(2);
        document.getElementById('m-costos').textContent = '$' + d.costos.toFixed(2);
        document.getElementById('m-utilidad').textContent = '$' + d.utilidad.toFixed(2);
        document.getElementById('m-inversion').textContent = '$' + d.inversion_activa.toFixed(2);
        document.getElementById('m-ventas-nuevas').textContent = d.ventas_nuevas.cantidad || 0;
        document.getElementById('m-ventas-nuevas-monto').textContent = '$' + (parseFloat(d.ventas_nuevas.monto) || 0).toFixed(2);
        document.getElementById('m-renovaciones').textContent = d.renovaciones.cantidad || 0;
        document.getElementById('m-renovaciones-monto').textContent = '$' + (parseFloat(d.renovaciones.monto) || 0).toFixed(2);
        document.getElementById('m-clientes').textContent = d.clientes_activos;
        document.getElementById('m-cuentas').textContent = d.cuentas_activas;
        document.getElementById('m-perfiles-v').textContent = d.perfiles_vendidos;
        document.getElementById('m-perfiles-d').textContent = d.perfiles_disponibles;
        document.getElementById('m-retencion').textContent = d.retencion + '%';
    }

    // Stock
    const stock = await apiGet('/api/dashboard/stock');
    if (stock.success) {
        const tbody = document.getElementById('stock-table');
        tbody.innerHTML = stock.data.map(s => {
            const color = s.ocupacion >= 90 ? 'bg-red-500' : s.ocupacion >= 80 ? 'bg-yellow-500' : 'bg-green-500';
            const alertDisp = parseInt(s.disponibles) <= 2 ? '<span class="ml-1 text-red-400 text-xs">BAJO</span>' : '';
            return `<tr class="border-b border-white/[0.04]">
                <td class="py-3 font-medium">${s.nombre}</td>
                <td class="py-3">${s.cuentas}</td>
                <td class="py-3">${s.perfiles}</td>
                <td class="py-3">${s.vendidos || 0}</td>
                <td class="py-3">${s.disponibles || 0}${alertDisp}</td>
                <td class="py-3"><div class="w-full bg-white/10 rounded-full h-2"><div class="${color} h-2 rounded-full" style="width:${Math.min(100,s.ocupacion)}%"></div></div><span class="text-xs text-muted">${s.ocupacion}%</span></td>
                <td class="py-3 text-red-400">$${parseFloat(s.costo).toFixed(2)}</td>
                <td class="py-3 text-green-400">$${s.ingreso_potencial.toFixed(2)}</td>
                <td class="py-3 text-accent">$${s.ganancia_neta.toFixed(2)}</td>
            </tr>`;
        }).join('');
    }

    // Ventas por cobrar
    const cobrar = await apiGet('/api/dashboard/ventas-cobrar');
    if (cobrar.success) {
        document.getElementById('ventas-cobrar').innerHTML = cobrar.data.length
            ? cobrar.data.map(v => `<div class="flex items-center justify-between p-3 bg-white/[0.02] rounded-lg">
                <div><p class="font-medium">${v.cliente_nombre}</p><p class="text-xs text-muted">${v.servicio_nombre} - $${parseFloat(v.precio_usd).toFixed(2)}</p></div>
                <a href="https://wa.me/${(v.cliente_telefono||'').replace(/\D/g,'')}" target="_blank" class="text-green-400 hover:text-green-300"><svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg></a>
            </div>`).join('')
            : '<p class="text-sm text-muted">No hay ventas por cobrar</p>';
    }

    // Cuentas por vencer
    const vencer = await apiGet('/api/dashboard/cuentas-vencer');
    if (vencer.success) {
        document.getElementById('cuentas-vencer').innerHTML = vencer.data.length
            ? vencer.data.map(c => `<div class="flex items-center justify-between p-3 bg-white/[0.02] rounded-lg">
                <div><p class="font-medium">${c.correo}</p><p class="text-xs text-muted">${c.servicio_nombre}</p></div>
                <span class="text-xs px-2 py-1 rounded bg-orange-500/10 text-orange-400">${c.fecha_vencimiento}</span>
            </div>`).join('')
            : '<p class="text-sm text-muted">No hay cuentas por vencer</p>';
    }

    // Ultimos pagos
    const pagos = await apiGet('/api/dashboard/ultimos-pagos');
    if (pagos.success) {
        document.getElementById('ultimos-pagos').innerHTML = pagos.data.length
            ? pagos.data.map(p => `<div class="flex items-center justify-between p-3 bg-white/[0.02] rounded-lg">
                <div><p class="font-medium">${p.cliente_nombre}</p><p class="text-xs text-muted">${p.servicio_nombre || ''}</p></div>
                <div class="text-right"><p class="font-medium text-green-400">$${parseFloat(p.monto_usd).toFixed(2)}</p>
                <span class="text-xs px-2 py-0.5 rounded ${p.venta_tipo === 'nueva' ? 'bg-blue-500/10 text-blue-400' : 'bg-accent/10 text-accent'}">${p.venta_tipo === 'nueva' ? 'Nueva' : 'Renovacion'}</span></div>
            </div>`).join('')
            : '<p class="text-sm text-muted">No hay pagos recientes</p>';
    }

    // Graficas
    if (typeof initDashboardCharts === 'function') {
        initDashboardCharts(stock.data || []);
    }
}
</script>
