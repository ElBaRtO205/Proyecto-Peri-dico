<x-admin-layout>

    <main class="panel-admin" id="panelAdmin">

        <!-- Encabezado del Panel -->
        <header class="panel-admin__header">
            <div>
                <h1 class="panel-admin__titulo">
                    <i class="fas fa-gauge-high"></i> Panel de Administración
                </h1>
                <p class="panel-admin__subtitulo">Gestión de contenidos — UMC News</p>
            </div>
            
            <form action="{{ route('logout') }}" method="POST" class="panel-admin__logout-form">
                @csrf
                <button type="submit" class="panel-admin__btn-salir">
                    <i class="fas fa-right-from-bracket"></i> Salir del Modo Admin
                </button>
            </form>
        </header>

        <!-- Alerta de Éxito -->
        @if(session('message'))
            <div class="alerta-exito">
                <i class="fas fa-check-circle"></i> {{ session('message') }}
            </div>
        @endif

        <!-- Botón para Crear Publicación -->
        <section class="panel-admin__acciones">
            <a href="{{ route('admin.noticias.create') }}" class="panel-admin__btn-nueva">
                <i class="fas fa-plus"></i> Nueva Publicación
            </a>
        </section>

        <!-- Sección de Noticias Existentes -->
        <section class="panel-admin__seccion">
            <h2 class="panel-admin__seccion-titulo">
                <i class="fas fa-newspaper"></i> Publicaciones Existentes
                <span class="contador" id="contadorPublicaciones">({{ $noticias->total() }})</span>
            </h2>
            
            <div class="grilla-admin" id="listaPublicaciones">
                @forelse($noticias as $noticia)
                    <article class="tarjeta-admin" data-id="{{ $noticia->id_noticia }}">
                        
                        <!-- Imagen + Badge Principal -->
                        <div class="tarjeta-admin__imagen-wrapper">
                            @if($noticia->imagen_noticia)
                                <img src="{{ asset('storage/' . $noticia->imagen_noticia) }}" alt="{{ $noticia->titulo }}" class="tarjeta-admin__imagen">
                            @else
                                <div class="tarjeta-admin__sin-imagen">
                                    <i class="fas fa-image"></i> Sin imagen
                                </div>
                            @endif

                            @if($noticia->es_principal)
                                <span class="badge-principal">★ Principal</span>
                            @endif
                        </div>
                        
                        <div class="tarjeta-admin__contenido">
                            <!-- Metadatos (Categoría y Status) -->
                            <div class="tarjeta-admin__meta">
                                <span class="badge-categoria">
                                    {{ $noticia->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                                <span class="badge-status badge-status--{{ $noticia->status ?? 'borrador' }}">
                                    {{ ucfirst($noticia->status ?? 'Borrador') }}
                                </span>
                            </div>

                            <h3 class="tarjeta-admin__titulo">{{ $noticia->titulo }}</h3>

                            <!-- Recorte de texto a 90 caracteres -->
                            <p class="tarjeta-admin__extracto">
                                {{ Str::limit(strip_tags($noticia->contenido), 90, '...') }}
                            </p>

                            <!-- Acciones -->
                            <footer class="tarjeta-admin__footer">
                                <a href="{{ route('admin.noticias.edit', $noticia) }}" class="btn-editar">
                                    <i class="fas fa-pen-to-square"></i> Editar
                                </a>

                                <form action="{{ route('admin.noticias.destroy', $noticia) }}" method="POST" 
                                      onsubmit="return confirm('¿Estás seguro de eliminar esta noticia?');" class="form-eliminar"> 
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-eliminar"> 
                                        <i class="fas fa-trash-can"></i> Eliminar
                                    </button>
                                </form>
                            </footer>
                        </div>
                    </article>
                @empty
                    <div class="sin-noticias">
                        <i class="fas fa-folder-open"></i>
                        <p>No hay noticias registradas aún.</p>
                    </div>
                @endforelse
            </div>

            <!-- Contenedor de Paginación corregida -->
            <div class="paginacion-contenedor">
                {{ $noticias->links('pagination::bootstrap-5') }}
            </div>
        </section>

    </main>

</x-admin-layout>