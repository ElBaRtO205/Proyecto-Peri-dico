/*
    Lógica de la portada: el reproductor del podcast en modo
    "picture-in-picture" flotante al hacer scroll.
*/
(function () {
    const videoSection = document.getElementById('videoSection');
    const videoPlayerContainer = document.getElementById('videoPlayerContainer');
    const closeFloatingBtn = document.getElementById('closeFloatingBtn');

    if (!videoSection || !videoPlayerContainer || !closeFloatingBtn) return;

    let isFloating = false;
    let userClosedIt = false; // Si el usuario lo cierra, no vuelve a aparecer hasta recargar

    function handleScroll() {
        if (userClosedIt) return;

        const rect = videoSection.getBoundingClientRect();

        // Cuando el video original sale de la pantalla, se vuelve flotante
        if (rect.bottom < 80) {
            if (!isFloating) {
                videoPlayerContainer.classList.add('is-floating');
                isFloating = true;
            }
        } else if (isFloating) {
            // Si el bloque original vuelve a ser visible, se recoloca
            videoPlayerContainer.classList.remove('is-floating');
            isFloating = false;
        }
    }

    closeFloatingBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        videoPlayerContainer.classList.remove('is-floating');
        isFloating = false;
        userClosedIt = true;
    });

    window.addEventListener('scroll', handleScroll);
    handleScroll();
})();
