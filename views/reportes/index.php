<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold font-['Syne']">Reportes</h2>
    <div class="flex gap-3">
        <input type="date" id="reporte-desde" class="input-field" value="<?= date('Y-m-01') ?>">
        <input type="date" id="reporte-hasta" class="input-field" value="<?= date('Y-m-d') ?>">
        <button onclick="cargarReportes()" class="px-4 py-2 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Filtrar</button>
    </div>
</div>

<!-- Resumen -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="glass rounded-xl p-5">
        <p class="text-sm text-muted mb-1">Clientes Nuevos</p>
        <p class="text-2xl font-bold text-blue-400" id="r-nuevos">0</p>
    </div>
    <div class="glass rounded-xl p-5">
        <p class="text-sm text-muted mb-1">Renovaciones</p>
        <p class="text-2xl font-bold text-green-400" id="r-renovaciones">0</p>
    </div>
    <div class="glass rounded-xl p-5">
        <p class="text-sm text-muted mb-1">Suspendidos</p>
        <p class="text-2xl font-bold text-yellow-400" id="r-suspendidos">0</p>
    </div>
    <div class="glass rounded-xl p-5">
        <p class="text-sm text-muted mb-1">Eliminados</p>
        <p class="text-2xl font-bold text-red-400" id="r-eliminados">0</p>
    </div>
</div>

<!-- Graficas -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-4">Ventas por Servicio</h3>
        <canvas id="chart-reporte-servicios" height="300"></canvas>
    </div>
    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-4">Ventas por Vendedor</h3>
        <canvas id="chart-reporte-vendedores" height="300"></canvas>
    </div>
</div>

<!-- Tabla de servicios -->
<div class="glass rounded-2xl p-6 mb-8">
    <h3 class="text-lg font-semibold font-['Syne'] mb-4">Desglose por Servicio</h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-muted border-b border-white/[0.08]">
                    <th class="pb-3">Servicio</th>
                    <th class="pb-3">Ventas</th>
                    <th class="pb-3">Ingresos</th>
                    <th class="pb-3">Vendidos</th>
                    <th class="pb-3">Disponibles</th>
                </tr>
            </thead>
            <tbody id="reporte-servicios-table"></tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', cargarReportes);

async function cargarReportes() {
    const desde = document.getElementById('reporte-desde').value;
    const hasta = document.getElementById('reporte-hasta').value;

    // Clientes stats
    const cRes = await apiGet(`/api/reportes/clientes?desde=${desde}&hasta=${hasta}`);
    if (cRes.success) {
        document.getElementById('r-nuevos').textContent = cRes.data.nuevos || 0;
        document.getElementById('r-renovaciones').textContent = cRes.data.renovaciones || 0;
        document.getElementById('r-suspendidos').textContent = cRes.data.suspendidos || 0;
        document.getElementById('r-eliminados').textContent = cRes.data.eliminados || 0;
    }

    // Servicios
    const sRes = await apiGet(`/api/reportes/servicios?desde=${desde}&hasta=${hasta}`);
    if (sRes.success) {
        document.getElementById('reporte-servicios-table').innerHTML = sRes.data.map(s => `
            <tr class="border-b border-white/[0.04]">
                <td class="py-3 font-medium">${s.servicio}</td>
                <td class="py-3">${s.ventas || 0}</td>
                <td class="py-3 text-green-400">$${parseFloat(s.ingresos || 0).toFixed(2)}</td>
                <td class="py-3">${s.perfiles_vendidos || 0}</td>
                <td class="py-3">${s.perfiles_disponibles || 0}</td>
            </tr>
        `).join('');

        if (typeof initReporteCharts === 'function') {
            initReporteCharts(sRes.data, []);
        }
    }

    // Vendedores chart
    const vRes = await apiGet(`/api/reportes/vendedores?desde=${desde}&hasta=${hasta}`);
    if (vRes.success && typeof initReporteCharts === 'function') {
        initReporteCharts(sRes.data || [], vRes.data || []);
    }
}
</script>
