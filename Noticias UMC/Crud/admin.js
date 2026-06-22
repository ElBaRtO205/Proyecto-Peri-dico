/*
    Lógica del panel de administración
    Aquí manejo las noticias: crear, editar, eliminar
    Por ahora uso datos de prueba, después se conecta con la base de datos
*/

// Datos de prueba (después vienen de la base de datos)
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

// Renderizar las tarjetas
function renderizarPublicaciones() {
    const contenedor = document.getElementById('listaPublicaciones');
    contenedor.innerHTML = '';

    publicaciones.forEach(pub => {
        const articulo = document.createElement('article');
        articulo.className = 'tarjeta-admin';
        articulo.setAttribute('data-id', pub.id);
        
        articulo.innerHTML = `
            <img src="${pub.imagen}" alt="${pub.titulo}" class="tarjeta-admin__imagen">
            
            <div class="tarjeta-admin__contenido">
                <header class="tarjeta-admin__header">
                    <h3 class="tarjeta-admin__titulo">${pub.titulo}</h3>
                    <time class="tarjeta-admin__fecha" datetime="${pub.fecha}">
                        <i class="fas fa-calendar-days"></i> ${new Date(pub.fecha).toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' })}
                    </time>
                </header>

                <p class="tarjeta-admin__extracto">${pub.extracto}</p>

                <footer class="tarjeta-admin__acciones">
                    <button class="btn-editar" onclick="editarPublicacion(${pub.id})">
                        <i class="fas fa-pen-to-square"></i> Editar
                    </button>
                    <button class="btn-eliminar" onclick="eliminarPublicacion(${pub.id})">
                        <i class="fas fa-trash-can"></i> Eliminar
                    </button>
                </footer>
            </div>
        `;
        contenedor.appendChild(articulo);
    });
}

// Nueva publicación
function nuevaPublicacion() {
    const titulo = prompt("✍️ Título de la nueva publicación:");
    if (!titulo) return;

    const extracto = prompt("✍️ Extracto corto de la noticia:");
    if (!extracto) return;

    const nueva = {
        id: Date.now(),
        titulo: titulo,
        fecha: new Date().toISOString().split('T')[0],
        imagen: "https://picsum.photos/id/" + Math.floor(Math.random()*100) + "/600/400",
        extracto: extracto
    };

    publicaciones.unshift(nueva);
    renderizarPublicaciones();
    alert("✅ Publicación creada correctamente");
}

// Editar publicación
function editarPublicacion(id) {
    const pub = publicaciones.find(p => p.id === id);
    if (!pub) return;

    const nuevoTitulo = prompt("✏️ Editar título:", pub.titulo);
    if (nuevoTitulo === null) return;

    const nuevoExtracto = prompt("✏️ Editar extracto:", pub.extracto);
    if (nuevoExtracto === null) return;

    pub.titulo = nuevoTitulo;
    pub.extracto = nuevoExtracto;
    renderizarPublicaciones();
}

// Eliminar publicación
function eliminarPublicacion(id) {
    if (!confirm("¿Estás seguro de eliminar esta publicación?")) return;

    publicaciones = publicaciones.filter(p => p.id !== id);
    renderizarPublicaciones();
}

// Salir del modo admin
function salirModoAdmin() {
    if (confirm("¿Salir del modo administración?")) {
        window.location.href = "index.html";
    }
}

// Toggle modo admin
function toggleModoAdmin() {
    document.getElementById('panelAdmin').style.display = 
        document.getElementById('panelAdmin').style.display === 'none' ? 'block' : 'none';
}

// Inicializar cuando cargue la página
document.addEventListener('DOMContentLoaded', () => {
    renderizarPublicaciones();
    
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