document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let saliendo = false;

    // Al volver con el botón del navegador, la página puede venir restaurada
    // desde la caché. Quitamos el estado de salida para que sea visible.
    window.addEventListener('pageshow', () => {
        saliendo = false;
        body.classList.remove('is-leaving');
    });

    // Solo animamos la salida hacia categorías y noticias: son las
    // navegaciones que el usuario percibe como "abrir" un contenido.
    const esDestinoAnimado = (url) =>
        /^\/(categoria|noticia)\//.test(url.pathname);

    document.addEventListener('click', (evento) => {
        if (saliendo || reduceMotion.matches) {
            return;
        }

        if (evento.defaultPrevented || evento.button !== 0) {
            return;
        }

        if (evento.metaKey || evento.ctrlKey || evento.shiftKey || evento.altKey) {
            return;
        }

        const enlace = evento.target.closest('a[href]');

        if (!enlace || enlace.hasAttribute('download')) {
            return;
        }

        if (enlace.target && enlace.target !== '_self') {
            return;
        }

        const url = new URL(enlace.href, window.location.href);

        if (url.origin !== window.location.origin) {
            return;
        }

        // Un ancla dentro de la misma página no debe provocar la transición.
        if (url.pathname === window.location.pathname && url.hash) {
            return;
        }

        if (!esDestinoAnimado(url)) {
            return;
        }

        evento.preventDefault();
        saliendo = true;
        body.classList.add('is-leaving');

        window.setTimeout(() => {
            window.location.assign(url.href);
        }, 200);
    });
});
