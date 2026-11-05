<x-app-layout>
    <x-slot:title>UMC News | {{ $categoria->nombre }}</x-slot:title>

    @push('head')
        <meta name="description" content="Noticias de {{ $categoria->nombre }} en UMC News, periódico oficial de la Universidad Marítima del Caribe.">
        <link rel="canonical" href="{{ route('noticia.categoria', $categoria) }}">
        <link rel="stylesheet" href="{{ asset('css/categoria-acertijo.css') }}">
    @endpush

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

            {{-- Solo aparece si internamente la categoría se llama Campus y el
             visitante tiene una cuenta registrada. --}}
        @if($categoria->nombre === 'Campus')
            @auth
                @if($acertijoHoy)
                <div class="susurro-acertijo-banner" id="acertijoCategoria"
                     data-desbloquear-url="{{ route('secreto.desbloquear') }}"
                     data-csrf="{{ csrf_token() }}">
                    <div class="susurro-acertijo__icono" id="btnAbrirAcertijoCat" title="Hay un murmullo en el campus...">
                        <i class="fas fa-wind"></i>
                    </div>
                    <div id="contenedorAcertijoCat" class="susurro-acertijo__cuerpo oculto">
                        <span class="susurro-acertijo__etiqueta">El Acertijo del Campus</span>
                        <p class="susurro-acertijo__pregunta">"{{ $acertijoHoy->pregunta }}"</p>
                        <div class="susurro-acertijo__form">
                            <label class="sr-only" for="inputClaveCat">Respuesta del acertijo</label>
                            <input type="text" id="inputClaveCat" placeholder="Susurra la respuesta...">
                            <button type="button" id="btnEnviarCat">Soplar</button>
                        </div>
                        <p id="msgAcertijoCat" class="susurro-acertijo__msg" role="status"></p>
                    </div>
                </div>

                <script src="{{ asset('js/categoria-acertijo.js') }}" defer></script>
                @endif
            @endauth
        @endif
        </header>

        <!-- Grilla de noticias en versión reducida -->
        @if($noticias->count() > 0)
            <div class="grilla-noticias grilla-noticias--categoria">
                @foreach($noticias as $noticia)
                    <article class="tarjeta-noticia tarjeta-noticia--compacta">
                        <a href="{{ route('noticia.show', $noticia) }}" class="tarjeta-noticia__vista-previa">
                            
                            <img class="tarjeta-noticia__imagen" 
                                 src="{{ $noticia->imagen_noticia ? asset('storage/' . $noticia->imagen_noticia) : asset('img/placeholder-noticia.svg') }}" 
                                 alt="{{ $noticia->titulo }}" 
                                 loading="lazy">
                            
                            <div class="tarjeta-noticia__contenido">
                                <h2 class="tarjeta-noticia__titulo">{{ $noticia->titulo }}</h2>
                                <p class="tarjeta-noticia__texto">{{ Str::limit($noticia->contenido, 110) }}</p>
                                
                                <div class="tarjeta-noticia__meta">
                                    <span class="tarjeta-noticia__autor">{{ $noticia->autor ?: 'Redacción UMC' }}</span>
                                    @if($noticia->fecha_publicacion)
                                        <time class="tarjeta-noticia__fecha" datetime="{{ $noticia->fecha_publicacion->format('Y-m-d') }}">
                                            {{ $noticia->fecha_publicacion->format('d/m/Y') }}
                                        </time>
                                    @endif
                                    <span class="tiempo-lectura">
                                        <i class="far fa-clock" aria-hidden="true"></i>
                                        {{ $noticia->tiempo_lectura }} min
                                    </span>
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