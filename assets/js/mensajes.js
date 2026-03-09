// WhatsApp message generator

function generarMensaje(plantilla, variables) {
    let mensaje = plantilla;
    for (const [key, value] of Object.entries(variables)) {
        mensaje = mensaje.replaceAll(`{${key}}`, value || '');
    }
    return mensaje;
}

function abrirWhatsApp(telefono, mensaje) {
    const tel = telefono.replace(/\D/g, '');
    const url = `https://wa.me/${tel}?text=${encodeURIComponent(mensaje)}`;
    window.open(url, '_blank');
}

function copiarMensaje(texto) {
    navigator.clipboard.writeText(texto).then(() => {
        showToast('Mensaje copiado al portapapeles.');
    });
}

// Generate collection message for a sale
function mensajeCobro(config, venta) {
    const plantilla = config.mensaje_cobro || 'Hola {cliente}, tu servicio de {servicio} vence el {vencimiento}. Precio: {precio}';
    return generarMensaje(plantilla, {
        cliente: venta.cliente_nombre,
        servicio: venta.servicio_nombre,
        vencimiento: venta.fecha_vencimiento,
        precio: '$' + parseFloat(venta.precio_usd).toFixed(2),
        precio_usd: '$' + parseFloat(venta.precio_usd).toFixed(2),
        banco: config.banco || '',
        telefono_pago: config.telefono_pago || '',
        cuenta_banco: config.cuenta_banco || '',
    });
}

// Generate delivery message
function mensajeEntrega(config, venta) {
    const plantilla = config.mensaje_entrega || 'Hola {cliente}, aqui tienes tu servicio de {servicio}: Cuenta: {cuenta} Contrasena: {password} Perfil: {perfil} PIN: {pin}';
    return generarMensaje(plantilla, {
        cliente: venta.cliente_nombre,
        servicio: venta.servicio_nombre,
        cuenta: venta.cuenta_correo,
        password: venta.cuenta_password || '',
        perfil: venta.numero_perfil,
        pin: venta.pin || '',
        vencimiento: venta.fecha_vencimiento,
        numero_orden: venta.numero_orden,
        precio: '$' + parseFloat(venta.precio_usd).toFixed(2),
    });
}

// Group collection message
function mensajeCobroGrupal(config, servicios, total) {
    const plantilla = config.mensaje_cobro_grupal || 'Hola {cliente}, estos son tus servicios:\n{servicios_lista}\n\nTotal: {total}';
    const lista = servicios.map(s => `- ${s.servicio}: $${parseFloat(s.precio).toFixed(2)} (${s.estado})`).join('\n');
    return generarMensaje(plantilla, {
        servicios_lista: lista,
        total: '$' + total.toFixed(2),
    });
}
