<x-app-layout>
    <x-slot:title>UMC News | Portada Principal</x-slot:title>

    <!-- ==================== NOTICIA PRINCIPAL ==================== -->
    <section class="hero">
        <h2 class="hero__titulo">Noticia principal</h2>

        @if($noticiaPrincipal)
            <div class="hero__vista-previa">
                <article class="hero__articulo">
                    <a href="{{ route('noticia.show', $noticiaPrincipal) }}" style="text-decoration: none; color: inherit; display: block;">
                        @if($noticiaPrincipal->imagen_noticia)
                            <img class="hero__imagen" src="{{ asset('storage/' . $noticiaPrincipal->imagen_noticia) }}" alt="{{ $noticiaPrincipal->titulo }}" loading="lazy">
                        @else
                            <img class="hero__imagen" src="{{ asset('img/placeholder-noticia.svg') }}" alt="Sin imagen" loading="lazy">
                        @endif

                        <h3 class="hero__subtitulo">{{ $noticiaPrincipal->titulo }}</h3>
                        <p class="hero__autor">{{ $noticiaPrincipal->autor ?: 'Redacción UMC' }}</p>
                        <time class="hero__fecha" datetime="{{ $noticiaPrincipal->fecha_publicacion?->format('Y-m-d') }}">
                            {{ $noticiaPrincipal->fecha_publicacion?->translatedFormat('d \d\e F, Y') }}
                        </time>
                        <p class="hero__texto">{{ Str::limit($noticiaPrincipal->contenido, 250) }}</p>
                    </a>
                </article>
            </div>
        @else
            <p style="text-align: center; padding: 2rem;">No hay noticias publicadas aún.</p>
        @endif
    </section>

    <!-- ==================== PODCAST ==================== -->
    <section class="seccion-video" id="videoSection">
        <h2 class="seccion-video__titulo"><i class="fab fa-youtube"></i> Podcast Institucional UMC</h2>

        <div class="video-bloque-wrapper">
            <div class="video-bloque" id="videoPlayerContainer">
                {{-- Para cambiar el episodio, reemplaza el ID del video de YouTube --}}
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

    <!-- ==================== SECCIONES POR CATEGORÍA ====================
         Se generan desde el controlador; aquí solo las recorremos.
         ================================================================= -->
    @foreach($secciones as $seccion)
        <section class="grilla-noticias">
            <h2 class="grilla-noticias__titulo">{{ $seccion['nombre'] }}</h2>

            @forelse($seccion['noticias'] as $noticia)
                <article class="tarjeta-noticia">
                    <a href="{{ route('noticia.show', $noticia) }}" class="tarjeta-noticia__vista-previa">
                        <img class="tarjeta-noticia__imagen"
                             src="{{ $noticia->imagen_noticia ? asset('storage/' . $noticia->imagen_noticia) : asset('img/placeholder-noticia.svg') }}"
                             alt="{{ $noticia->titulo }}" loading="lazy">
                        <h3 class="tarjeta-noticia__titulo">{{ $noticia->titulo }}</h3>
                        <p class="tarjeta-noticia__texto">{{ Str::limit($noticia->contenido, 150) }}</p>
                        <p class="tarjeta-noticia__autor">{{ $noticia->autor ?: 'Redacción UMC' }}</p>
                        <time class="tarjeta-noticia__fecha" datetime="{{ $noticia->fecha_publicacion?->format('Y-m-d') }}">
                            {{ $noticia->fecha_publicacion?->format('d/m/Y') }}
                        </time>
                        <span class="tiempo-lectura">
                            <i class="far fa-clock" aria-hidden="true"></i>
                            {{ $noticia->tiempo_lectura }} min
                        </span>
                    </a>
                </article>
            @empty
                <p>Pronto nuevas noticias de {{ $seccion['nombre'] }}.</p>
            @endforelse

            <div class="grilla-noticias__ver-mas">
                <a href="{{ route('noticia.categoria', $seccion['categoria']) }}"
                   class="grilla-noticias__enlace-mas">
                    Ver más noticias de {{ $seccion['nombre'] }}
                </a>
            </div>
        </section>
    @endforeach

    <!-- ==================== SELLO OCULTO: SOPLAR AL VIENTO ====================
         Solo lo ven las personas registradas. Para entrar también deben
         resolver el acertijo activo. -->
    @auth
        @php
            $acertijoActual = \App\Models\Acertijo::where('activo', true)->orderByDesc('id_acertijo')->first();
            $desbloqueadoHoy = ($acertijoActual && session('acertijo_resuelto_id') == $acertijoActual->id_acertijo)
                || auth()->user()?->isAdmin();
        @endphp

        <div class="sello-misterioso-container">
            @if($desbloqueadoHoy)
                <a href="{{ route('secreto.index') }}" class="sello-misterioso sello-misterioso--activo" title="Entrar a Soplar al Viento">
                    <img src="{{ asset('img/umc_news_logo.svg') }}" alt="Sello UMC" class="sello-misterioso__icono">
                    <span class="sello-misterioso__texto">Soplar al viento...</span>
                </a>
            @else
                <div class="sello-misterioso sello-misterioso--bloqueado" title="El viento guarda silencio...">
                    <img src="{{ asset('img/umc_news_logo.svg') }}" alt="Sello UMC" class="sello-misterioso__icono">
                </div>
            @endif
        </div>
    @endauth
</x-app-layout>
