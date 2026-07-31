<x-app-layout>
    <x-slot:title>UMC News | Portada Principal</x-slot:title>
        
        <!-- Noticia principal destacada -->
        <section class="hero">
            <h2 class="hero__titulo">Noticia principal</h2>
            
            @if($noticiaPrincipal)
            <div class="hero__vista-previa">
                <article class="hero__articulo">
                    <a href="{{ route('noticia.show', $noticiaPrincipal->id_noticia) }}" style="text-decoration: none; color: inherit; display: block;">
                        @if($noticiaPrincipal->imagen_noticia)
                            <img class="hero__imagen" src="{{ asset('storage/' . $noticiaPrincipal->imagen_noticia) }}" alt="{{ $noticiaPrincipal->titulo }}" loading="lazy">
                        @else
                            <img class="hero__imagen" src="{{ asset('img/noticia destacada/noticias-destacadas.jpg') }}" alt="Imagen por defecto" loading="lazy">
                        @endif
                        
                        <h3 class="hero__subtitulo">{{ $noticiaPrincipal->titulo }}</h3>
                        <p class="hero__autor">{{ $noticiaPrincipal->autor->nombre ?? 'Redacción UMC' }}</p>
                        <time class="hero__fecha" datetime="{{ $noticiaPrincipal->fecha_publicacion->format('Y-m-d') }}">{{ $noticiaPrincipal->fecha_publicacion->format('d \d\e F \d\e Y') }}</time>
                        <p class="hero__texto">{{ Str::limit($noticiaPrincipal->texto ?? $noticiaPrincipal->contenido, 250) }}</p>
                    </a>
                </article>
            </div>
            @else
            <p style="text-align: center; padding: 2rem;">No hay noticias publicadas aún.</p>
            @endif
        </section>

        <!-- ==================== SECCION: PODCAST ==================== -->
        <section class="seccion-video" id="videoSection">
            <h2 class="seccion-video__titulo"><i class="fab fa-youtube"></i> Podcast Institucional UMC</h2>
            
            <div class="video-bloque-wrapper">
                <div class="video-bloque" id="videoPlayerContainer">  */ se cambia el "watch" del link por "embed", https://www.youtube.com/watch?v=-C3b15ldP9s*/
                    <iframe 
                        src="https://www.youtube.com/embed/JtYxFsbSeaQ" 
                        title="Podcast UMC" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen>
                    </iframe>
                    <button class="pip-close-btn-yt" id="closeFloatingBtn" title="Cerrar">✕</button>
                </div>
            </div>
        </section>

        <!-- Seccion de deportes -->
        <section class="grilla-noticias">
            <h2 class="grilla-noticias__titulo">Deportes</h2>
            
            @forelse($noticias->where('categoria.nombre', 'deporte')->take(2) as $noticia)
            <article class="tarjeta-noticia">
                <a href="{{ route('noticia.show', $noticia->id_noticia) }}" class="tarjeta-noticia__vista-previa">
                    <img class="tarjeta-noticia__imagen" src="{{ $noticia->imagen_noticia ? asset('storage/' . $noticia->imagen_noticia) : asset('img/deportes/deportes.jpg') }}" alt="{{ $noticia->titulo }}" loading="lazy">
                    <h3 class="tarjeta-noticia__titulo">{{ $noticia->titulo }}</h3>
                    <p class="tarjeta-noticia__texto">{{ Str::limit($noticia->texto ?? $noticia->contenido, 150) }}</p>
                    <p class="tarjeta-noticia__autor">{{ $noticia->autor->nombre ?? 'Redacción' }}</p>
                    <time class="tarjeta-noticia__fecha" datetime="{{ $noticia->fecha_publicacion->format('Y-m-d') }}">{{ $noticia->fecha_publicacion->format('d/m/Y') }}</time>
                </a>
            </article>
            @empty
                <p>Pronto nuevas noticias de deportes.</p>
            @endforelse
            
            <div class="grilla-noticias__ver-mas">
                @php
                    $idDeporte = $noticias->where('categoria.nombre', 'deporte')->first()?->id_categoria;
                @endphp
                <a href="{{ $idDeporte ? route('noticia.categoria', $idDeporte) : '#' }}" class="grilla-noticias__enlace-mas">
                    Ver más noticias de Deportes
                </a>
            </div>
        </section>

        <!-- Seccion de cultura -->
        <section class="grilla-noticias">
            <h2 class="grilla-noticias__titulo">Cultura</h2>
            
            @forelse($noticias->where('categoria.nombre', 'cultura')->take(2) as $noticia)
            <article class="tarjeta-noticia">
                <a href="{{ route('noticia.show', $noticia->id_noticia) }}" class="tarjeta-noticia__vista-previa">
                    <img class="tarjeta-noticia__imagen" src="{{ $noticia->imagen_noticia ? asset('storage/' . $noticia->imagen_noticia) : asset('img/cultura/venezuela-arte.jpg') }}" alt="{{ $noticia->titulo }}" loading="lazy">
                    <h3 class="tarjeta-noticia__titulo">{{ $noticia->titulo }}</h3>
                    <p class="tarjeta-noticia__texto">{{ Str::limit($noticia->texto ?? $noticia->contenido, 150) }}</p>
                    <p class="tarjeta-noticia__autor">{{ $noticia->autor->nombre ?? 'Redacción' }}</p>
                    <time class="tarjeta-noticia__fecha" datetime="{{ $noticia->fecha_publicacion->format('Y-m-d') }}">{{ $noticia->fecha_publicacion->format('d/m/Y') }}</time>
                </a>
            </article>
            @empty
                <p>Pronto nuevas noticias de Cultura.</p>
            @endforelse
            
            <div class="grilla-noticias__ver-mas">
                @php
                    $idCultura = $noticias->where('categoria.nombre', 'cultura')->first()?->id_categoria;
                @endphp
                <a href="{{ $idCultura ? route('noticia.categoria', $idCultura) : '#' }}" class="grilla-noticias__enlace-mas">
                    Ver más noticias de Cultura
                </a>
            </div>
        </section>

        <!-- Seccion economia -->
        <section class="grilla-noticias">
            <h2 class="grilla-noticias__titulo">Economia</h2>
            
            @forelse($noticias->where('categoria.nombre', 'Economia')->take(4) as $noticia)
            <article class="tarjeta-noticia">
                <a href="{{ route('noticia.show', $noticia->id_noticia) }}" class="tarjeta-noticia__vista-previa">
                    <img class="tarjeta-noticia__imagen" src="{{ $noticia->imagen_noticia ? asset('storage/' . $noticia->imagen_noticia) : asset('img/Economia/estudiantes.jpg') }}" alt="{{ $noticia->titulo }}" loading="lazy">
                    <h3 class="tarjeta-noticia__titulo">{{ $noticia->titulo }}</h3>
                    <p class="tarjeta-noticia__texto">{{ Str::limit($noticia->texto ?? $noticia->contenido, 150) }}</p>
                    <p class="tarjeta-noticia__autor">{{ $noticia->autor->nombre ?? 'Redacción' }}</p>
                    <time class="tarjeta-noticia__fecha" datetime="{{ $noticia->fecha_publicacion->format('Y-m-d') }}">{{ $noticia->fecha_publicacion->format('d/m/Y') }}</time>
                </a>
            </article>
            @empty
                <p>Pronto nuevas noticias de Economia.</p>
            @endforelse
            
            <div class="grilla-noticias__ver-mas">
                @php
                    $idEconomia = $noticias->where('categoria.nombre', 'Economia')->first()?->id_categoria;
                @endphp
                <a href="{{ $idEconomia ? route('noticia.categoria', $idEconomia) : '#' }}" class="grilla-noticias__enlace-mas">
                    Ver más noticias de Economia
                </a>
            </div>
        </section>

        <!-- Seccion rumores del campus -->
        <section class="grilla-noticias">
            <h2 class="grilla-noticias__titulo">Rumores del Campus</h2>
            
            @forelse($noticias->where('categoria.nombre', 'rumores')->take(2) as $noticia)
            <article class="tarjeta-noticia">
                <a href="{{ route('noticia.show', $noticia->id_noticia) }}" class="tarjeta-noticia__vista-previa">
                    <img class="tarjeta-noticia__imagen" src="{{ asset('storage/' . $noticia->imagen_noticia) }}" alt="{{ $noticia->titulo }}" loading="lazy">
                    <h3 class="tarjeta-noticia__titulo">{{ $noticia->titulo }}</h3>
                    <p class="tarjeta-noticia__texto">{{ Str::limit($noticia->texto ?? $noticia->contenido, 150) }}</p>
                    <p class="tarjeta-noticia__autor">{{ $noticia->autor->nombre ?? 'Redacción' }}</p>
                    <time class="tarjeta-noticia__fecha" datetime="{{ $noticia->fecha_publicacion->format('Y-m-d') }}">{{ $noticia->fecha_publicacion->format('d/m/Y') }}</time>
                </a>
            </article>
            @empty
                <p>Pronto nuevos rumores del campus.</p>
            @endforelse
            
            <div class="grilla-noticias__ver-mas">
                @php
                    $idRumores = $noticias->where('categoria.nombre', 'rumores')->first()?->id_categoria;
                @endphp
                <a href="{{ $idRumores ? route('noticia.categoria', $idRumores) : '#' }}" class="grilla-noticias__enlace-mas">
                    Ver más noticias de Rumores del Campus
                </a>
            </div>
        </section>
</x-app-layout>