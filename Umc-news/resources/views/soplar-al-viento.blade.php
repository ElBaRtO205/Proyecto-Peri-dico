<x-app-layout>
    <x-slot:title>Soplar al Viento | Rincón Secreto UMC</x-slot:title>

    @push('head')
        <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Lora:ital,wght@0,400;0,500;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/soplar-al-viento.css') }}">
    @endpush

    <div class="viento-wrapper" id="vientoWrapper"
         data-soplar-base="{{ url('/soplar-al-viento') }}"
         data-csrf="{{ csrf_token() }}">
        <!-- Partículas / Líneas de viento decorativas -->
        <div class="viento-bg-lines">
            <span class="linea-viento l1"></span>
            <span class="linea-viento l2"></span>
            <span class="linea-viento l3"></span>
        </div>

        <div class="viento-container">
            <!-- Barra superior -->
            <div class="viento-top-nav">
                <div class="viento-nav__volver">
                    <a href="{{ route('home') }}" class="btn-regresar-portada">
                        <i class="fas fa-compass"></i> Volver a tierra firme (Portada)
                    </a>

                    {{-- Vuelta rápida al panel, solo para administradores --}}
                    @auth
                        @if(auth()->user()?->isAdmin())
                            <a href="{{ route('admin.noticias.index') }}" class="btn-panel-admin">
                                <i class="fas fa-gauge-high"></i> Volver al panel de admin
                            </a>

                            <form action="{{ route('admin.secreto.limpiar') }}" method="POST"
                                  class="form-limpiar-viento"
                                  data-confirmar="Esto borrará todos los susurros, el acertijo y la clave de acceso. ¿Quieres limpiar todo?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-limpiar-viento"
                                        title="Borrar susurros, acertijo y clave de acceso">
                                    <i class="fas fa-broom" aria-hidden="true"></i> Limpiar todo
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>

                <span class="viento-estado">
                    <i class="fas fa-wind"></i> Brisa activa · Los secretos aquí escritos pertenecen al viento
                </span>
            </div>

            <!-- Cabecera Poética -->
            <header class="viento-header">
                <div class="viento-icono-central">
                    <i class="fas fa-feather-alt"></i>
                </div>
                <h1 class="viento-titulo">Soplar al Viento</h1>
                <p class="viento-subtitulo">
                    Un refugio anónimo para quienes encontraron la clave. Deja tus confesiones, rumores, versos o desahogos universitarios antes de que cambie la marea.
                </p>
            </header>

            @if(session('status'))
                <div class="viento-alerta">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            @auth
                @if(auth()->user()?->isAdmin() && ! $acertijoActivo)
                    <div class="viento-aviso-admin">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        No hay acertijo activo. La sección queda solo para administradores hasta que crees uno nuevo.
                    </div>
                @endif
            @endauth

            <!-- ==================== FORMULARIO PARA LANZAR UN SUSURRO ==================== -->
            <section class="viento-compositor">
                <form action="{{ route('secreto.publicar') }}" method="POST" class="compositor-card">
                    @csrf
                    <div class="compositor-top">
                        <div class="input-apodo-wrap">
                            <i class="fas fa-mask"></i>
                            <input type="text" name="apodo" maxlength="40" placeholder="Seudónimo (Ej: Marinero Fantasma, Catia 2026...)">
                        </div>
                        <select name="categoria_viento" class="select-tipo-susurro" required>
                            <option value="Confesión">🌊 Confesión</option>
                            <option value="Rumor">👀 Rumor del Campus</option>
                            <option value="Desahogo">⛈️ Desahogo</option>
                            <option value="Verso">📜 Verso / Nota</option>
                        </select>
                    </div>

                    <textarea name="contenido" id="contenidoSusurro" rows="3" maxlength="500" required
                              placeholder="Escribe aquello que solo le contarías al viento..."></textarea>

                    <div class="compositor-bottom">
                        <span id="contadorLetras" class="contador-letras">0/500</span>
                        <button type="submit" class="btn-soplar">
                            <i class="fas fa-paper-plane"></i> Lanzar al viento
                        </button>
                    </div>
                </form>
            </section>

            <!-- ==================== FILTROS RÁPIDOS ==================== -->
            <div class="viento-filtros" role="group" aria-label="Filtrar susurros por categoría">
                <button type="button" class="filtro-btn activo" data-filtro="Todos">Todos los murmullos</button>
                <button type="button" class="filtro-btn" data-filtro="Confesión">🌊 Confesiones</button>
                <button type="button" class="filtro-btn" data-filtro="Rumor">👀 Rumores</button>
                <button type="button" class="filtro-btn" data-filtro="Desahogo">⛈️ Desahogos</button>
                <button type="button" class="filtro-btn" data-filtro="Verso">📜 Versos</button>
            </div>

            <!-- ==================== MURO DE SUSURROS ==================== -->
            <section class="muro-susurros" id="muroSusurros">
                @forelse($susurros as $susurro)
                    <article class="tarjeta-susurro" data-categoria="{{ $susurro->categoria_viento }}">
                        <div class="susurro-cabecera">
                            <span class="susurro-autor">
                                <i class="fas fa-user-secret"></i> {{ $susurro->apodo }}
                            </span>
                            <span class="susurro-badge badge--{{ strtolower($susurro->categoria_viento) }}">
                                {{ $susurro->categoria_viento }}
                            </span>
                        </div>

                        <p class="susurro-texto">“{{ $susurro->contenido }}”</p>

                        <div class="susurro-pie">
                            <time class="susurro-tiempo">
                                {{ $susurro->fecha_publicacion ? $susurro->fecha_publicacion->diffForHumans() : 'Hace poco' }}
                            </time>

                            <div class="susurro-acciones">
                                <button type="button"
                                        class="btn-dar-aliento {{ session('soplado_'.$susurro->id_susurro) ? 'soplado' : '' }}"
                                        data-susurro-id="{{ $susurro->id_susurro }}"
                                        title="Dar aliento a este susurro"
                                        aria-label="Dar aliento a este susurro">
                                    <i class="fas fa-wind" aria-hidden="true"></i>
                                    <span class="conteo-soplos">{{ $susurro->soplos }}</span>
                                </button>

                                @auth
                                    @if(auth()->user()?->isAdmin())
                                        <form action="{{ route('admin.susurro.destroy', $susurro->id_susurro) }}" method="POST" class="form-borrar-susurro">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-borrar-susurro" title="Disipar (Borrar)" aria-label="Eliminar susurro">✕</button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="viento-vacio">
                        <i class="fas fa-wind"></i>
                        <p>El viento está en silencio absoluto. Sé el primero en dejar un murmullo.</p>
                    </div>
                @endforelse
            </section>

            <div class="viento-paginacion">
                {{ $susurros->links() }}
            </div>
        </div>
    </div>

    <script src="{{ asset('js/soplar-al-viento.js') }}" defer></script>

</x-app-layout>