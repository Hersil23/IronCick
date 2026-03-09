// Chart.js initialization for dashboard and reports

let chartVentasServicio = null;
let chartIngresos = null;
let chartReporteServicios = null;
let chartReporteVendedores = null;

const chartColors = [
    '#ea580c', '#f97316', '#22c55e', '#3b82f6',
    '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4',
    '#ec4899', '#14b8a6',
];

function initDashboardCharts(stockData) {
    const nombres = stockData.map(s => s.nombre);
    const vendidos = stockData.map(s => parseInt(s.vendidos) || 0);
    const disponibles = stockData.map(s => parseInt(s.disponibles) || 0);

    // Bar chart - Ventas por Servicio
    const barCtx = document.getElementById('chart-ventas-servicio');
    if (barCtx) {
        if (chartVentasServicio) chartVentasServicio.destroy();
        chartVentasServicio = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: nombres,
                datasets: [
                    {
                        label: 'Vendidos',
                        data: vendidos,
                        backgroundColor: '#ea580c',
                        borderRadius: 6,
                    },
                    {
                        label: 'Disponibles',
                        data: disponibles,
                        backgroundColor: 'rgba(255,255,255,0.1)',
                        borderRadius: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { labels: { color: '#94a3b8' } },
                },
                scales: {
                    x: { ticks: { color: '#64748b' }, grid: { color: 'rgba(255,255,255,0.04)' } },
                    y: { ticks: { color: '#64748b' }, grid: { color: 'rgba(255,255,255,0.04)' } },
                },
            },
        });
    }

    // Doughnut - Distribucion de Ingresos
    const donutCtx = document.getElementById('chart-ingresos');
    if (donutCtx) {
        const ingresos = stockData.map(s => parseFloat(s.precio_usd) * (parseInt(s.vendidos) || 0));
        if (chartIngresos) chartIngresos.destroy();
        chartIngresos = new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: nombres,
                datasets: [{
                    data: ingresos,
                    backgroundColor: chartColors.slice(0, nombres.length),
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#94a3b8', padding: 12 },
                    },
                },
            },
        });
    }
}

function initReporteCharts(serviciosData, vendedoresData) {
    // Servicios bar chart
    const sCtx = document.getElementById('chart-reporte-servicios');
    if (sCtx && serviciosData.length) {
        if (chartReporteServicios) chartReporteServicios.destroy();
        chartReporteServicios = new Chart(sCtx, {
            type: 'bar',
            data: {
                labels: serviciosData.map(s => s.servicio),
                datasets: [{
                    label: 'Ingresos USD',
                    data: serviciosData.map(s => parseFloat(s.ingresos) || 0),
                    backgroundColor: '#ea580c',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                plugins: { legend: { labels: { color: '#94a3b8' } } },
                scales: {
                    x: { ticks: { color: '#64748b' }, grid: { color: 'rgba(255,255,255,0.04)' } },
                    y: { ticks: { color: '#64748b' }, grid: { color: 'rgba(255,255,255,0.04)' } },
                },
            },
        });
    }

    // Vendedores bar chart
    const vCtx = document.getElementById('chart-reporte-vendedores');
    if (vCtx && vendedoresData.length) {
        if (chartReporteVendedores) chartReporteVendedores.destroy();
        chartReporteVendedores = new Chart(vCtx, {
            type: 'bar',
            data: {
                labels: vendedoresData.map(v => v.vendedor || 'Panel'),
                datasets: [{
                    label: 'Ingresos USD',
                    data: vendedoresData.map(v => parseFloat(v.ingresos) || 0),
                    backgroundColor: '#3b82f6',
                    borderRadius: 6,
                }],
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                plugins: { legend: { labels: { color: '#94a3b8' } } },
                scales: {
                    x: { ticks: { color: '#64748b' }, grid: { color: 'rgba(255,255,255,0.04)' } },
                    y: { ticks: { color: '#64748b' }, grid: { color: 'rgba(255,255,255,0.04)' } },
                },
            },
        });
    }
}
