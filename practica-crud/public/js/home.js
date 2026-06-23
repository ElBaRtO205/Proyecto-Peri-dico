
        // Esperar a que el documento cargue completamente
        document.addEventListener('DOMContentLoaded', function () {

            // Botón de modo admin
            const btnAdmin = document.querySelector('.barra-superior__btn-admin');

            if (btnAdmin) {
                btnAdmin.addEventListener('click', function () {
                    // Alternar la clase 'modo-admin' en el body
                    document.body.classList.toggle('modo-admin');

                    // Cambiar el texto y estilo del botón
                    if (document.body.classList.contains('modo-admin')) {
                        this.innerHTML = '<i class="fas fa-xmark"></i> Salir Admin';
                        this.classList.add('activo');
                    } else {
                        this.innerHTML = '<i class="fas fa-gear"></i> Admin';
                        this.classList.remove('activo');
                    }
                });
            }

            // Botón de búsqueda
            const btnBuscar = document.querySelector('.encabezado__btn-buscar');
            const barraBusqueda = document.querySelector('.encabezado__busqueda');
            const inputBuscar = document.querySelector('.encabezado__input-buscar');
            const btnCerrarBuscar = document.querySelector('.encabezado__btn-cerrar-buscar');

            // Abrir búsqueda
            if (btnBuscar && barraBusqueda) {
                btnBuscar.addEventListener('click', function () {
                    barraBusqueda.classList.add('activo');
                    // Enfocar el input automáticamente
                    setTimeout(() => {
                        inputBuscar.focus();
                    }, 100);
                });
            }

            // Cerrar búsqueda
            if (btnCerrarBuscar && barraBusqueda) {
                btnCerrarBuscar.addEventListener('click', function () {
                    barraBusqueda.classList.remove('activo');
                    inputBuscar.value = '';
                });
            }

            // Cerrar búsqueda con tecla Escape
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

            // Preview de imágenes en formularios
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

            // Confirmación de eliminación
            document.querySelectorAll('.formulario__btn--eliminar').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (confirm('¿Estás seguro de que deseas eliminar esta noticia? Esta acción no se puede deshacer.')) {
                        alert('Noticia eliminada (simulación)');
                    }
                });
            });

            // Confirmación de cancelación
            document.querySelectorAll('.formulario__btn--cancelar').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (confirm('¿Descartar los cambios?')) {
                        location.reload();
                    }
                });
            });
        });

        // Función para desactivar modo admin (usada por el botón cancelar del hero)
        function desactivarModoAdminHero() {
            const btn = document.querySelector('.barra-superior__btn-admin');
            document.body.classList.remove('modo-admin');
            if (btn) {
                btn.textContent = '⚙️ Admin';
                btn.classList.remove('activo');
            }
        }