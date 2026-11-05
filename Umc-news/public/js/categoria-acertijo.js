document.addEventListener('DOMContentLoaded', () => {
    const banner = document.getElementById('acertijoCategoria');

    if (!banner) {
        return;
    }

    const botonAbrir = document.getElementById('btnAbrirAcertijoCat');
    const contenedor = document.getElementById('contenedorAcertijoCat');
    const botonEnviar = document.getElementById('btnEnviarCat');
    const input = document.getElementById('inputClaveCat');
    const mensaje = document.getElementById('msgAcertijoCat');
    const urlDesbloquear = banner.dataset.desbloquearUrl;
    const csrfToken = banner.dataset.csrf;

    botonAbrir?.addEventListener('click', () => {
        contenedor.classList.toggle('oculto');

        if (!contenedor.classList.contains('oculto')) {
            input?.focus();
        }
    });

    const enviarRespuesta = async () => {
        if (!input?.value.trim()) {
            return;
        }

        try {
            const respuesta = await fetch(urlDesbloquear, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ clave: input.value }),
            });

            const datos = await respuesta.json();
            mensaje.textContent = datos.mensaje;
            mensaje.classList.add('visible');
            mensaje.classList.toggle('error', !datos.exito);
            mensaje.classList.toggle('exito', Boolean(datos.exito));

            if (datos.exito) {
                window.location.reload();
            }
        } catch (error) {
            mensaje.textContent = 'No se pudo comprobar la respuesta. Inténtalo de nuevo.';
            mensaje.classList.add('visible', 'error');
        }
    };

    botonEnviar?.addEventListener('click', enviarRespuesta);
    input?.addEventListener('keydown', (evento) => {
        if (evento.key === 'Enter') {
            evento.preventDefault();
            enviarRespuesta();
        }
    });
});
