document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('vientoWrapper');

    if (!wrapper) {
        return;
    }

    const soplarBase = wrapper.dataset.soplarBase;
    const csrfToken = wrapper.dataset.csrf;
    const textarea = document.getElementById('contenidoSusurro');
    const contador = document.getElementById('contadorLetras');

    // Contador de caracteres del compositor.
    if (textarea && contador) {
        const actualizarContador = () => {
            contador.textContent = `${textarea.value.length}/500`;
        };

        textarea.addEventListener('input', actualizarContador);
        actualizarContador();
    }

    // Filtros rápidos por categoría.
    const filtros = document.querySelectorAll('.filtro-btn');
    const tarjetas = document.querySelectorAll('.tarjeta-susurro');

    filtros.forEach((boton) => {
        boton.addEventListener('click', () => {
            const categoria = boton.dataset.filtro;

            filtros.forEach((otro) => otro.classList.remove('activo'));
            boton.classList.add('activo');

            tarjetas.forEach((tarjeta) => {
                const visible = categoria === 'Todos' || tarjeta.dataset.categoria === categoria;
                tarjeta.classList.toggle('oculta', !visible);
            });
        });
    });

    // Dar "Aliento" (like) vía AJAX.
    async function darAliento(id, boton) {
        try {
            const respuesta = await fetch(`${soplarBase}/${id}/soplar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            if (!respuesta.ok) {
                return;
            }

            const datos = await respuesta.json();
            boton.querySelector('.conteo-soplos').textContent = datos.soplos;
            boton.classList.add('soplado');
        } catch (error) {
            console.error('No se pudo dar aliento al susurro.', error);
        }
    }

    document.querySelectorAll('.btn-dar-aliento').forEach((boton) => {
        boton.addEventListener('click', () => {
            darAliento(boton.dataset.susurroId, boton);
        });
    });

    // Confirmación para acciones destructivas (por ejemplo, limpiar todo).
    document.querySelectorAll('form[data-confirmar]').forEach((formulario) => {
        formulario.addEventListener('submit', (evento) => {
            if (!window.confirm(formulario.dataset.confirmar)) {
                evento.preventDefault();
            }
        });
    });
});
