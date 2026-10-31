<x-app-layout>
    <x-slot:title>{{ $noticia->titulo }} | UMC News</x-slot:title>

    @php
        $urlCanonical = route('noticia.show', $noticia);
        $urlImagen = $noticia->imagen_noticia
            ? asset('storage/' . $noticia->imagen_noticia)
            : asset('img/placeholder-noticia.svg');
        $resumen = Str::limit(strip_tags($noticia->contenido), 155);
        $fechaISO = \Carbon\Carbon::parse($noticia->fecha_publicacion)->toIso8601String();
    @endphp

    @push('head')
        {{-- SEO básico --}}
        <meta name="description" content="{{ $resumen }}">
        <link rel="canonical" href="{{ $urlCanonical }}">

        {{-- Open Graph (Facebook, WhatsApp, LinkedIn...) --}}
        <meta property="og:type" content="article">
        <meta property="og:site_name" content="UMC News">
        <meta property="og:locale" content="es_VE">
        <meta property="og:title" content="{{ $noticia->titulo }}">
        <meta property="og:description" content="{{ $resumen }}">
        <meta property="og:url" content="{{ $urlCanonical }}">
        <meta property="og:image" content="{{ $urlImagen }}">

        {{-- Twitter / X Cards --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $noticia->titulo }}">
        <meta name="twitter:description" content="{{ $resumen }}">
        <meta name="twitter:image" content="{{ $urlImagen }}">

        {{-- Datos estructurados: artículo para buscadores --}}
        @php
            $datosSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'NewsArticle',
                'headline' => $noticia->titulo,
                'description' => $resumen,
                'image' => [$urlImagen],
                'datePublished' => $fechaISO,
                'dateModified' => $fechaISO,
                'author' => ['@type' => 'Person', 'name' => $noticia->autor ?: 'Redacción UMC'],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'UMC News',
                    'logo' => ['@type' => 'ImageObject', 'url' => asset('img/umc_news_logo.svg')],
                ],
                'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $urlCanonical],
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($datosSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Lora:ital,wght@0,400;0,500;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/stilos-noticias-vista.css') }}">

<main class="article">

<div class="crumb-row" style="max-width: 1100px; margin: 0 auto; padding: 1rem 0; display: flex; justify-content: flex-start; width: 100%; position: relative; top: 30px; z-index: 10;">
    <a class="btn-volver" href="{{ url('/') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; white-space: nowrap; width: fit-content; border-radius: 8px; text-decoration: none;">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px; min-width: 16px;"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Volver a UMC News
    </a>
  </div>
 <div class="hero" style="display: flex; justify-content: center; padding: 2rem 1rem; position: relative; background: transparent;">
    
    {{-- Imagen encuadrada sin opacidad --}}
    @if($noticia->imagen_noticia)
      <img src="{{ asset('storage/' . $noticia->imagen_noticia) }}" 
           style="width: 100%; max-width: 1100px; max-height: 550px; object-fit: cover; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); position: relative; z-index: 2;" 
           alt="Imagen principal de la noticia">
    @endif
    
    {{-- Etiqueta de la categoría --}}
    <div class="hero-chip" style="z-index: 3; position: absolute; bottom: 10px; left: 5%; background: var(--surface); color: var(--text);">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M8 10l2 2 4-5"/></svg>
      {{ $noticia->categoria->nombre ?? 'UMC News' }}
    </div>
    
  </div>

  <div class="article-grid">

    <aside class="share-rail" aria-label="Compartir">
      <button title="WhatsApp" onclick="pulse(this)">
        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2zm5.7 14.3c-.2.6-1.4 1.2-2 1.3-.5.1-1.1.1-1.8-.1-.4-.1-1-.3-1.7-.6-3-1.3-4.9-4.3-5.1-4.5-.1-.2-1.2-1.6-1.2-3.1 0-1.4.7-2.1 1-2.4.3-.3.6-.4.8-.4h.6c.2 0 .4 0 .6.5.2.5.7 1.8.8 1.9.1.2.1.3 0 .5-.1.2-.1.3-.3.5l-.4.5c-.1.2-.3.3-.1.6.2.3.9 1.4 1.9 2.3 1.3 1.1 2.4 1.5 2.7 1.6.3.1.5.1.7-.1.2-.2.8-.9 1-1.2.2-.3.4-.2.7-.1.3.1 1.7.8 2 1 .3.1.5.2.6.3.1.2.1.7-.1 1.3z"/></svg>
      </button>
      <button title="Copiar enlace" onclick="copyLink(this)">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 012-2h10"/></svg>
      </button>
    </aside>

    <div class="col">
      <div class="kicker">Categoría: {{ $noticia->categoria->nombre ?? 'General' }}</div>

      <h1 class="headline">{{ $noticia->titulo }}</h1>

      <div class="byline">
        <div class="avatar">{{ strtoupper(substr($noticia->autor ?? 'U', 0, 1)) }}</div>
        <div class="byline-text">
          Por <b>{{ ucwords($noticia->autor ?? 'Redacción UMC') }}</b>
          <span class="sep">·</span>{{ \Carbon\Carbon::parse($noticia->fecha_publicacion)->translatedFormat('d \d\e F, Y') }}
        </div>
      </div>

      {{-- Generamos un resumen automático con las primeras 150 letras del contenido --}}
      <p class="dek">{{ Str::limit(strip_tags($noticia->contenido), 150) }}</p>

      <div class="prose">
        {{-- Aquí se imprime el contenido HTML de tu noticia --}}
        {!! $noticia->contenido !!}

        {{-- Infobox adaptado usando los datos de la Categoría que sí tienes en BD --}}
        <div class="infobox">
          <h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
            Datos clave de la sección
          </h3>
          <ul>
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></svg>
              <span><b>Tema central:</b> {{ $noticia->categoria->nombre ?? 'Universidad' }}</span>
            </li>
            <li>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l2.3 5.6L20 9.3l-4.5 3.6L17 19l-5-3.4L7 19l1.5-6.1L4 9.3l5.7-.7z"/></svg>
              <span><b>Enfoque:</b> {{ $noticia->categoria->descripcion ?? 'Información de interés general.' }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <section class="related">
    <div class="related-head">
      <h3>Sigue leyendo</h3>
      <span>Más de UMC News</span>
    </div>
    <div class="related-grid">

      {{-- Bucle para mostrar las 3 noticias extraídas de la base de datos --}}
      @foreach($noticias_relacionadas as $relacionada)
      <article class="rcard">
        {{-- Usamos id_noticia porque así se llama en tu tabla --}}
        <a href="{{ route('noticia.show', $relacionada) }}" style="text-decoration: none; color: inherit;">
          <div class="thumb">
            @if($relacionada->imagen_noticia)
              <img src="{{ asset('storage/'.$relacionada->imagen_noticia) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px;">
            @else
              <svg viewBox="0 0 400 250" xmlns="http://www.w3.org/2000/svg">
                <rect width="400" height="250" fill="#1f3a5f"/>
              </svg>
            @endif
          </div>
          <div class="rbody">
            <div class="rcat">{{ $relacionada->categoria->nombre ?? 'General' }}</div>
            <h4>{{ $relacionada->titulo }}</h4>
          </div>
        </a>
      </article>
      @endforeach

    </div>
  </section>

  {{-- =========================================================
       SECCIÓN DE COMENTARIOS
       ========================================================= --}}
  <div class="comments-container">
    <section class="seccion-comentarios" id="comentarios">
      <h3 class="seccion-comentarios__titulo">
        <i class="fas fa-comments"></i> Comentarios ({{ $noticia->comentarios->count() }})
      </h3>

      {{-- Mensaje de éxito --}}
      @if(session('exito'))
        <div class="comentario-alerta comentario-alerta--exito">
          <i class="fas fa-check-circle"></i> {{ session('exito') }}
        </div>
      @endif

      {{-- Mensajes de error --}}
      @if($errors->any())
        <div class="comentario-alerta comentario-alerta--error">
          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Formulario para publicar --}}
      <form action="{{ route('comentarios.store', $noticia->id_noticia) }}" method="POST" class="form-comentario">
        @csrf
        <div class="form-comentario__grupo">
          <input type="text" name="autor" class="form-comentario__input" value="{{ old('autor') }}"
                 placeholder="Tu nombre o apodo (Ej. Estudiante de Informática)" required>
        </div>
        <div class="form-comentario__grupo">
          <textarea name="contenido" class="form-comentario__textarea" rows="3"
                    placeholder="Escribe un comentario o contribución sobre este tema..." required>{{ old('contenido') }}</textarea>
        </div>
        <button type="submit" class="btn-comentar">
          <i class="fas fa-paper-plane"></i> Publicar Comentario
        </button>
      </form>

      {{-- Listado de comentarios --}}
      <div class="lista-comentarios">
        @forelse($noticia->comentarios as $comentario)
          <article class="tarjeta-comentario">
            <div class="tarjeta-comentario__cabecera">
              <div class="tarjeta-comentario__avatar">
                {{ strtoupper(substr($comentario->autor ?? 'U', 0, 1)) }}
              </div>
              <div class="tarjeta-comentario__meta">
                <strong class="tarjeta-comentario__autor">{{ $comentario->autor }}</strong>
                <time class="tarjeta-comentario__fecha">{{ optional($comentario->created_at)->diffForHumans() }}</time>
              </div>
            </div>
            <p class="tarjeta-comentario__texto">{{ $comentario->contenido }}</p>
          </article>
        @empty
          <div class="comentarios-vacios">
            <p><i class="far fa-comment-dots"></i> No hay comentarios todavía. Sé el primero en opinar sobre esta publicación.</p>
          </div>
        @endforelse
      </div>
    </section>
  </div>

</main>

 <script src="{{ asset('js/botones-cuerpo-noticia.js') }}" defer></script>

</x-app-layout>