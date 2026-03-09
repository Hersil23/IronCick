<h2 class="text-xl font-bold font-['Syne'] mb-6">Configuracion</h2>

<div class="space-y-6">
    <!-- General -->
    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-4">General</h3>
        <form id="form-config-general" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-1">WhatsApp de contacto</label>
                    <input type="text" name="whatsapp_contacto" class="input-field w-full" placeholder="+58 412 1234567">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Moneda principal</label>
                    <select name="moneda" class="input-field w-full">
                        <?php foreach (MONEDAS as $code => $name): ?>
                        <option value="<?= $code ?>"><?= $code ?> - <?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Fuente de tasa</label>
                    <select name="tasa_fuente" class="input-field w-full">
                        <option value="paralelo">Paralelo</option>
                        <option value="bcv">BCV</option>
                        <option value="binance">Binance</option>
                        <option value="manual">Manual</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Tasa manual (si aplica)</label>
                    <input type="number" step="0.01" name="tasa_manual" class="input-field w-full" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Margen de recargo %</label>
                    <input type="number" step="0.1" name="margen_recargo" class="input-field w-full" placeholder="0">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Dias para suspender cliente</label>
                    <input type="number" name="dias_suspension" class="input-field w-full" value="90">
                </div>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Guardar General</button>
        </form>
    </div>

    <!-- Datos de pago -->
    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-4">Datos de Pago</h3>
        <form id="form-config-pago" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Banco</label>
                    <input type="text" name="banco" class="input-field w-full">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Telefono pago movil</label>
                    <input type="text" name="telefono_pago" class="input-field w-full">
                </div>
                <div>
                    <label class="block text-sm text-slate-400 mb-1">Numero de cuenta</label>
                    <input type="text" name="cuenta_banco" class="input-field w-full">
                </div>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Guardar Pago</button>
        </form>
    </div>

    <!-- Mensajes WhatsApp -->
    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-4">Mensajes WhatsApp</h3>
        <p class="text-xs text-muted mb-4">Variables disponibles: {cliente} {apellido} {servicio} {cuenta} {password} {perfil} {pin} {fecha_compra} {vencimiento} {precio} {precio_usd} {banco} {telefono_pago} {cuenta_banco} {numero_orden} {vendedor}</p>
        <form id="form-config-mensajes" class="space-y-4">
            <?php
            $mensajes = [
                'mensaje_entrega' => 'Mensaje de Entrega',
                'mensaje_cambio' => 'Mensaje de Cambio',
                'mensaje_cobro' => 'Mensaje de Cobro',
                'mensaje_cobro_grupal' => 'Mensaje de Cobro Grupal',
                'mensaje_renovacion' => 'Mensaje de Renovacion',
                'mensaje_renovacion_grupal' => 'Mensaje de Renovacion Grupal',
                'mensaje_alerta_3dias' => 'Alerta 3 dias antes',
                'mensaje_alerta_1dia' => 'Alerta 1 dia antes',
                'mensaje_alerta_hoy' => 'Alerta dia que vence',
                'mensaje_bienvenida' => 'Bienvenida cliente nuevo',
                'mensaje_recarga' => 'Recarga de creditos',
                'mensaje_imap' => 'Codigo de verificacion IMAP',
            ];
            foreach ($mensajes as $key => $label):
            ?>
            <div>
                <label class="block text-sm text-slate-400 mb-1"><?= $label ?></label>
                <textarea name="<?= $key ?>" class="input-field w-full" rows="3"></textarea>
            </div>
            <?php endforeach; ?>
            <button type="submit" class="px-6 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Guardar Mensajes</button>
        </form>
    </div>

    <!-- Logo tienda -->
    <div class="glass rounded-2xl p-6">
        <h3 class="text-lg font-semibold font-['Syne'] mb-4">Logo de Tienda</h3>
        <form id="form-logo" enctype="multipart/form-data" class="flex items-center gap-4">
            <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" class="input-field">
            <button type="submit" class="px-6 py-2.5 bg-accent hover:bg-accent-hover text-white rounded-lg text-sm font-medium">Subir</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async function() {
    const res = await apiGet('/api/configuracion');
    if (!res.success) return;
    const config = res.data;

    // Populate forms
    const forms = ['form-config-general', 'form-config-pago', 'form-config-mensajes'];
    forms.forEach(formId => {
        const form = document.getElementById(formId);
        if (!form) return;
        for (const [key, value] of Object.entries(config)) {
            const input = form.elements[key];
            if (input) input.value = value || '';
        }
    });
});

// General
document.getElementById('form-config-general').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this));
    await apiPost('/api/configuracion/guardar', data);
    showToast('Configuracion general guardada.');
});

// Pago
document.getElementById('form-config-pago').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this));
    await apiPost('/api/configuracion/guardar', data);
    showToast('Datos de pago guardados.');
});

// Mensajes
document.getElementById('form-config-mensajes').addEventListener('submit', async function(e) {
    e.preventDefault();
    const data = Object.fromEntries(new FormData(this));
    await apiPost('/api/configuracion/guardar', data);
    showToast('Mensajes guardados.');
});

// Logo
document.getElementById('form-logo').addEventListener('submit', async function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    const res = await fetch('/api/configuracion/upload-logo', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getCSRF() },
        body: fd,
    });
    const data = await res.json();
    if (data.success) showToast('Logo subido.');
    else showToast(data.message, 'error');
});
</script>
