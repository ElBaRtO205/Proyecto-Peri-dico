/*
    Lógica del panel de administración
    Aquí manejo las noticias: crear, editar, eliminar
    Por ahora uso datos de prueba, después se conecta con la base de datos
*/

// Datos de prueba (después vienen de la base de datos)
/*
let publicaciones = [
    {
        id: 1,
        titulo: "Nueva investigación marina gana premio internacional",
        fecha: "2026-05-20",
        imagen: "https://picsum.photos/id/1015/600/400",
        extracto: "Estudiantes de la UMC presentan avances en monitoreo de corales en el Caribe."
    },
    {
        id: 2,
        titulo: "Equipo de fútbol clasifica a final nacional",
        fecha: "2026-05-22",
        imagen: "https://picsum.photos/id/201/600/400",
        extracto: "Los Delfines de la UMC vencieron en semifinales con un gol en el último minuto."
    }
];
*/
// Salir del modo admin
function salirModoAdmin() {
    if (confirm("¿Salir del modo administración?")) {
        window.location.href = "/"; // Redirige a la página principal o de inicio
    }
}

// Toggle modo admin
function toggleModoAdmin() {
    document.getElementById('panelAdmin').style.display = 
        document.getElementById('panelAdmin').style.display === 'none' ? 'block' : 'none';
}

// Inicializar cuando cargue la página
document.addEventListener('DOMContentLoaded', () => {
    
    // ============================================
    // BOTÓN DE BÚSQUEDA
    // ============================================
    const btnBuscar = document.querySelector('.encabezado__btn-buscar');
    const barraBusqueda = document.querySelector('.encabezado__busqueda');
    const inputBuscar = document.querySelector('.encabezado__input-buscar');
    const btnCerrarBuscar = document.querySelector('.encabezado__btn-cerrar-buscar');
    
    // Abrir búsqueda
    if (btnBuscar && barraBusqueda) {
        btnBuscar.addEventListener('click', function () {
            barraBusqueda.classList.add('activo');
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
    
    // Buscar publicaciones en las tarjetas de admin
    if (inputBuscar) {
        inputBuscar.addEventListener('input', function () {
            const termino = this.value.toLowerCase().trim();
            const tarjetas = document.querySelectorAll('.tarjeta-admin');
            
            tarjetas.forEach(tarjeta => {
                const titulo = tarjeta.querySelector('.tarjeta-admin__titulo').textContent.toLowerCase();
                const extracto = tarjeta.querySelector('.tarjeta-admin__extracto').textContent.toLowerCase();
                
                if (termino === '') {
                    tarjeta.style.display = '';
                } else if (titulo.includes(termino) || extracto.includes(termino)) {
                    tarjeta.style.display = '';
                } else {
                    tarjeta.style.display = 'none';
                }
            });
        });
    }
});