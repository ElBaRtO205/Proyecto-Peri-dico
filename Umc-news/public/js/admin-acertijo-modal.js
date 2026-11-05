document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modalAcertijoAdmin');

    if (!modal) {
        return;
    }

    const abrir = document.querySelector('[data-abrir-acertijo]');
    const cerrar = modal.querySelectorAll('[data-cerrar-acertijo]');
    const primerCampo = modal.querySelector('textarea, input');

    const mostrar = () => {
        modal.hidden = false;
        primerCampo?.focus();
    };

    const ocultar = () => {
        modal.hidden = true;
        abrir?.focus();
    };

    abrir?.addEventListener('click', mostrar);
    cerrar.forEach((boton) => boton.addEventListener('click', ocultar));

    modal.addEventListener('click', (evento) => {
        if (evento.target === modal) {
            ocultar();
        }
    });

    document.addEventListener('keydown', (evento) => {
        if (evento.key === 'Escape' && !modal.hidden) {
            ocultar();
        }
    });
});
