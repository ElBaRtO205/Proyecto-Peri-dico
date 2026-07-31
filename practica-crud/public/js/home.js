
        // Esperar a que el documento cargue completamente
        document.addEventListener('DOMContentLoaded', function () {

            // Botón de busqueda
            const btnBuscar = document.querySelector('.encabezado__btn-buscar');
            const barraBusqueda = document.querySelector('.encabezado__busqueda');
            const inputBuscar = document.querySelector('.encabezado__input-buscar');
            const btnCerrarBuscar = document.querySelector('.encabezado__btn-cerrar-buscar');

            // Abrir busqueda
            if (btnBuscar && barraBusqueda) {
                btnBuscar.addEventListener('click', function () {
                    barraBusqueda.classList.add('activo');
                    // Enfocar el input automáticamente
                    setTimeout(() => {
                        inputBuscar.focus();
                    }, 100);
                });
            }

            // Cerrar busqueda
            if (btnCerrarBuscar && barraBusqueda) {
                btnCerrarBuscar.addEventListener('click', function () {
                    barraBusqueda.classList.remove('activo');
                    inputBuscar.value = '';
                });
            }

            // Cerrar busqueda con tecla Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && barraBusqueda.classList.contains('activo')) {
                    barraBusqueda.classList.remove('activo');
                    inputBuscar.value = '';
                }
            });

            // Buscar noticias
            if (inputBuscar) {
                inputBuscar.addEventListener('input', function () {
                    const termino = this.value.toLowerCase().trim();
                    const tarjetas = document.querySelectorAll('.tarjeta-noticia');

                    tarjetas.forEach(tarjeta => {
                        const titulo = tarjeta.querySelector('.tarjeta-noticia__titulo').textContent.toLowerCase();
                        const texto = tarjeta.querySelector('.tarjeta-noticia__texto').textContent.toLowerCase();

                        if (termino === '') {
                            tarjeta.style.display = '';
                        } else if (titulo.includes(termino) || texto.includes(termino)) {
                            tarjeta.style.display = '';
                        } else {
                            tarjeta.style.display = 'none';
                        }
                    });
                });
            }

            // Preview de imagenes en formularios
            document.querySelectorAll('.formulario__input-file').forEach(input => {
                input.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const preview = input.parentElement.querySelector('img');
                            if (preview) {
                                preview.src = e.target.result;
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });

            // Confirmacion de eliminacion
            document.querySelectorAll('.formulario__btn--eliminar').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (confirm('¿Estás seguro de que deseas eliminar esta noticia? Esta acción no se puede deshacer.')) {
                        alert('Noticia eliminada (simulación)');
                    }
                });
            });

            // Confirmacion de cancelacion
            document.querySelectorAll('.formulario__btn--cancelar').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (confirm('¿Descartar los cambios?')) {
                        location.reload();
                    }
                });
            });
        });

        // Funcion para desactivar modo admin (usada por el botón cancelar del hero)
        function desactivarModoAdminHero() {
            const btn = document.querySelector('.barra-superior__btn-admin');
            document.body.classList.remove('modo-admin');
            if (btn) {
                btn.textContent = '⚙️ Admin';
                btn.classList.remove('activo');
            }
        }
//funciones para el video picture-in-picture

(function() {
    const videoSection = document.getElementById('videoSection');
    const videoPlayerContainer = document.getElementById('videoPlayerContainer');
    const closeFloatingBtn = document.getElementById('closeFloatingBtn');

    if (!videoSection || !videoPlayerContainer) return;

    let isFloating = false;
    let userClosedIt = false; // Si el usuario lo cierra, no vuelve a joder hasta que recargue

    function handleScroll() {
        if (userClosedIt) return;

        const rect = videoSection.getBoundingClientRect();

        // Si la parte inferior del video original sale de la pantalla superior (borde < 80px)
        if (rect.bottom < 80) {
            if (!isFloating) {
                videoPlayerContainer.classList.add('is-floating');
                isFloating = true;
            }
        } else {
            // Si el usuario vuelve a subir y el bloque original es visible de nuevo
            if (isFloating) {
                videoPlayerContainer.classList.remove('is-floating');
                isFloating = false;
            }
        }
    }

    // Evento para el botón cerrar
    closeFloatingBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        videoPlayerContainer.classList.remove('is-floating');
        isFloating = false;
        userClosedIt = true; // Evita que se vuelva a activar al hacer scroll
    });

    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Verificación inicial
})();