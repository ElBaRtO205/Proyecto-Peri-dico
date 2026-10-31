document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
    // 1. CONTROL DE TEMA (MODO OSCURO / CLARO)
    // -------------------------------------------------------------
    const themeBtn = document.getElementById('btn-theme-toggle');
    const themeIcon = document.getElementById('icono-tema');
    const themeText = document.getElementById('texto-tema');
    
    // Cargar preferencia guardada o tema del sistema
    const savedTheme = localStorage.getItem('umc_theme');
    const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    const currentTheme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
    
    function aplicarTema(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('umc_theme', theme);
        
        if (theme === 'dark') {
            themeIcon.className = 'fas fa-sun';
            themeText.textContent = 'Modo Claro';
        } else {
            themeIcon.className = 'fas fa-moon';
            themeText.textContent = 'Modo Oscuro';
        }
    }

    aplicarTema(currentTheme);

    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            aplicarTema(isDark ? 'light' : 'dark');
        });
    }

    // -------------------------------------------------------------
    // 2. CONTROL DE TAMAÑO DE FUENTE
    // -------------------------------------------------------------
    const btnUp = document.getElementById('btn-font-up');
    const btnDown = document.getElementById('btn-font-down');
    const btnReset = document.getElementById('btn-font-reset');

    // Nivel actual en porcentaje (100% = base)
    let fontScale = parseInt(localStorage.getItem('umc_font_scale')) || 100;

    function aplicarTamanoFuente(scale) {
        // Límites razonables de accesibilidad (85% a 130%)
        if (scale < 85) scale = 85;
        if (scale > 130) scale = 130;

        fontScale = scale;
        document.documentElement.style.setProperty('--tamano-fuente-base', `${fontScale}%`);
        localStorage.setItem('umc_font_scale', fontScale);
    }

    // Cargar escala inicial
    aplicarTamanoFuente(fontScale);

    if (btnUp) btnUp.addEventListener('click', () => aplicarTamanoFuente(fontScale + 10));
    if (btnDown) btnDown.addEventListener('click', () => aplicarTamanoFuente(fontScale - 10));
    if (btnReset) btnReset.addEventListener('click', () => aplicarTamanoFuente(100));
});

document.addEventListener('DOMContentLoaded', () => {
    // 1. Copiar Enlace al Portapapeles
    const btnCopiar = document.getElementById('btn-copiar-enlace');
    const textoCopiar = document.getElementById('texto-copiar');

    if (btnCopiar) {
        btnCopiar.addEventListener('click', () => {
            navigator.clipboard.writeText(window.location.href).then(() => {
                textoCopiar.textContent = '¡Enlace Copiado!';
                btnCopiar.style.backgroundColor = '#16a34a';

                setTimeout(() => {
                    textoCopiar.textContent = 'Copiar Enlace';
                    btnCopiar.style.backgroundColor = '';
                }, 2000);
            });
        });
    }

    // 2. Efecto visual al hacer clic en Reacciones
    const btnsReaccion = document.querySelectorAll('.btn-reaccion');
    btnsReaccion.forEach(btn => {
        btn.addEventListener('click', () => {
            const contador = btn.querySelector('.btn-reaccion__contador');
            let valor = parseInt(contador.textContent);

            if (btn.classList.contains('activa')) {
                btn.classList.remove('activa');
                contador.textContent = valor - 1;
            } else {
                btn.classList.add('activa');
                contador.textContent = valor + 1;
            }
        });
    });
});