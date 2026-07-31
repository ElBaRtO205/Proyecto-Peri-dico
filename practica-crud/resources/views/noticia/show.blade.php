<x-app-layout>
<body>

    <!-- CONTENEDOR PRINCIPAL EDITORIAL -->
    <main class="articulo-layout">
        
        <!-- Botón de regreso -->
        <a href="{{ route('home') }}" class="noticia-regresar">
            <i class="fas fa-arrow-left"></i> Volver a UMC News
        </a>

        <article>
            <!-- 1. ENCABEZADO DE LA NOTICIA -->
            <header class="noticia-header">
                <span class="noticia-categoria-badge">
                    {{ $noticia->categoria->nombre ?? 'General' }}
                </span>

                <h1 class="noticia-titulo">{{ $noticia->titulo }}</h1>

                {{-- Calculo de tiempo estimado de lectura --}}
                @php
                    $textoLimpio = strip_tags($noticia->contenido ?? $noticia->texto);
                    $totalPalabras = str_word_count($textoLimpio);
                    $minutosLectura = max(1, (int) ceil($totalPalabras / 200));
                @endphp

                <!-- Barra de Metadatos -->
                <div class="noticia-meta-bar">
                    <div class="noticia-meta-item">
                        <i class="fas fa-user-edit"></i>
                        <span>Por: <strong>{{ $noticia->autor->nombre ?? $noticia->autor ?? 'Redacción UMC' }}</strong></span>
                    </div>
                    <div class="noticia-meta-item">
                        <i class="far fa-calendar-alt"></i>
                        <span>{{ optional($noticia->fecha_publicacion)->format('d \d\e F, Y') }}</span>
                    </div>
                    <div class="noticia-meta-item">
                        <i class="far fa-clock"></i>
                        <span>{{ $minutosLectura }} {{ $minutosLectura === 1 ? 'minuto' : 'minutos' }} de lectura</span>
                    </div>
                </div>
            </header>

            <!-- 2. IMAGEN DESTACADA -->
            @if($noticia->imagen_noticia)
                <div class="noticia-imagen-wrapper">
                    <img src="{{ asset('storage/' . $noticia->imagen_noticia) }}" class="noticia-imagen-principal" alt="{{ $noticia->titulo }}">
                </div>
            @endif

            <!-- 3. CUERPO DE LA NOTICIA (Con soporte para Búsqueda del Tesoro) -->
            <div class="noticia-cuerpo" id="cuerpo-noticia" data-palabra="{{ strtoupper($tesoroHoy->palabra ?? '') }}">
                {{ $noticia->contenido ?? $noticia->texto }}
            </div>

            <!-- =========================================================
                 4. MODULO DE COMPARTIR EN REDES
               ========================================================= -->
            <section class="seccion-editorial noticia-compartir">
                <h3 class="seccion-titulo"><i class="fas fa-share-nodes"></i> Compartir este artículo</h3>
                <div class="compartir-grid noticia-compartir__botones">
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($noticia->titulo . ' - ' . request()->fullUrl()) }}" 
                       target="_blank" rel="noopener noreferrer" class="btn-red btn-red--whatsapp">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>

                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($noticia->titulo) }}&url={{ urlencode(request()->fullUrl()) }}" 
                       target="_blank" rel="noopener noreferrer" class="btn-red btn-red--x">
                        <i class="fab fa-x-twitter"></i> Twitter / X
                    </a>

                    <a href="https://t.me/share/url?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($noticia->titulo) }}" 
                       target="_blank" rel="noopener noreferrer" class="btn-red btn-red--telegram">
                        <i class="fab fa-telegram"></i> Telegram
                    </a>

                    <button id="btn-copiar-enlace" class="btn-red btn-red--copiar">
                        <i class="fas fa-link"></i> <span id="texto-copiar">Copiar Enlace</span>
                    </button>
                </div>
            </section>

            <!-- =========================================================
                 5. REACCIONES RAPIDAS DE LECTORES
               ========================================================= -->
            <section class="seccion-editorial noticia-reacciones">
                <h4 class="seccion-titulo"><i class="far fa-thumbs-up"></i> Reacciones de la comunidad</h4>
                <div class="noticia-reacciones__contenedor">
                    <button class="btn-reaccion" data-tipo="interesante">
                        <span class="btn-reaccion__emoji">👍</span>
                        <span class="btn-reaccion__etiqueta">Interesante</span>
                        <span class="btn-reaccion__contador">12</span>
                    </button>

                    <button class="btn-reaccion" data-tipo="orgullo">
                        <span class="btn-reaccion__emoji">⚓</span>
                        <span class="btn-reaccion__etiqueta">Orgullo UMC</span>
                        <span class="btn-reaccion__contador">28</span>
                    </button>

                    <button class="btn-reaccion" data-tipo="importante">
                        <span class="btn-reaccion__emoji">💡</span>
                        <span class="btn-reaccion__etiqueta">Útil</span>
                        <span class="btn-reaccion__contador">5</span>
                    </button>
                </div>
            </section>

            <!-- =========================================================
                 6. SECCIÓN DE COMENTARIOS
               ========================================================= -->
            <section class="seccion-editorial seccion-comentarios" id="comentarios">
                <h3 class="seccion-titulo">
                    <i class="fas fa-comments"></i> Comentarios ({{ count($noticia->comentarios ?? []) }})
                </h3>

                <!-- Mensaje de Exito -->
                @if(session('exito'))
                    <div style="background: #d1e7dd; color: #0f5132; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border: 1px solid #badbcc;">
                        <i class="fas fa-check-circle"></i> {{ session('exito') }}
                    </div>
                @endif

                <!-- Mensaje de error -->
                @if($errors->any())
                    <div style="background: #f8d7da; color: #842029; padding: 1rem; border-radius: 6px; margin-bottom: 1.5rem; border: 1px solid #f5c2c7;">
                        <ul style="margin: 0; padding-left: 1.2rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Formulario -->
                <form action="{{ route('comentarios.store', $noticia->id_noticia) }}" method="POST" class="form-comentario">
                    @csrf
                    <div class="form-comentario__grupo">
                        <input type="text" name="autor" class="form-comentario__input" placeholder="Tu nombre o apodo (Ej. Estudiante de Informática)" required>
                    </div>
                    <div class="form-comentario__grupo">
                        <textarea name="contenido" class="form-comentario__textarea" rows="3" placeholder="Escribe un comentario o contribución sobre este tema..." required></textarea>
                    </div>
                    <button type="submit" class="btn-comentar">
                        <i class="fas fa-paper-plane"></i> Publicar Comentario
                    </button>
                </form>

                <!-- Listado de Comentarios -->
                <div class="lista-comentarios" style="margin-top: 2rem;">
                    @forelse($noticia->comentarios ?? [] as $comentario)
                        <article class="tarjeta-comentario" style="margin-bottom: 1.25rem;">
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
        </article>

    </main>

    <script src="{{ asset('js/busqueda-tesoro.js') }}" defer></script>

</x-app-layout>