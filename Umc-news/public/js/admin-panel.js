document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('adminSidebar');
    const toggle = document.querySelector('[data-admin-menu-toggle]');
    const overlay = document.querySelector('.admin-overlay');
    const closeButtons = document.querySelectorAll('[data-admin-menu-close]');

    if (!sidebar || !toggle) {
        return;
    }

    const abrir = () => {
        sidebar.classList.add('is-open');
        overlay?.removeAttribute('hidden');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    };

    const cerrar = () => {
        sidebar.classList.remove('is-open');
        overlay?.setAttribute('hidden', '');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    };

    toggle.addEventListener('click', () => {
        sidebar.classList.contains('is-open') ? cerrar() : abrir();
    });

    closeButtons.forEach((boton) => {
        boton.addEventListener('click', cerrar);
    });

    document.addEventListener('keydown', (evento) => {
        if (evento.key === 'Escape' && sidebar.classList.contains('is-open')) {
            cerrar();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 900) {
            cerrar();
        }
    });
});
