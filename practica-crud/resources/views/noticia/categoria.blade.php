<x-app-layout>
    <x-slot:title>UMC News | {{ $categoria->nombre }}</x-slot:title>

    <div class="categoria-container">
        <!-- Botón para regresar -->
        <div class="categoria-header-nav">
            <a href="{{ route('home') }}" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver al Inicio
            </a>
        </div>

        <!-- Título de la sección -->
        <header class="categoria-titulo-bloque">
            <h1 class="categoria-titulo">Sección: {{ $categoria->nombre }}</h1>
        </header>

        <!-- Grilla de noticias en versión reducida -->
        @if($noticias->count() > 0)
            <div class="grilla-noticias grilla-noticias--categoria">
                @foreach($noticias as $noticia)
                    <article class="tarjeta-noticia tarjeta-noticia--compacta">
                        <a href="{{ route('noticia.show', $noticia->id_noticia) }}" class="tarjeta-noticia__vista-previa">
                            
                            <img class="tarjeta-noticia__imagen" 
                                 src="{{ $noticia->imagen_noticia ? asset('storage/' . $noticia->imagen_noticia) : asset('img/noticias-destacadas.jpg') }}" 
                                 alt="{{ $noticia->titulo }}" 
                                 loading="lazy">
                            
                            <div class="tarjeta-noticia__contenido">
                                <h2 class="tarjeta-noticia__titulo">{{ $noticia->titulo }}</h2>
                                <p class="tarjeta-noticia__texto">{{ Str::limit($noticia->texto ?? $noticia->contenido, 110) }}</p>
                                
                                <div class="tarjeta-noticia__meta">
                                    <span class="tarjeta-noticia__autor">{{ $noticia->autor->nombre ?? 'Redacción UMC' }}</span>
                                    @if($noticia->fecha_publicacion)
                                        <time class="tarjeta-noticia__fecha" datetime="{{ $noticia->fecha_publicacion->format('Y-m-d') }}">
                                            {{ $noticia->fecha_publicacion->format('d/m/Y') }}
                                        </time>
                                    @endif
                                </div>
                            </div>

                        </a>
                    </article>
                @endforeach
            </div>

            <!-- Paginación de Laravel -->
            <div class="categoria-paginacion">
                {{ $noticias->links() }}
            </div>
        @else
            <div class="categoria-vacia">
                <p><i class="fas fa-info-circle"></i> No hay noticias publicadas en esta sección todavía.</p>
            </div>
        @endif
    </div>
</x-app-layout>