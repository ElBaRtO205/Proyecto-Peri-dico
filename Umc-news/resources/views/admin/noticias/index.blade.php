<x-admin-layout>

    @php
        $acertijoActualAdmin = \App\Models\Acertijo::where('activo', true)->orderByDesc('id_acertijo')->first();
        $totalPublicaciones = $noticias->total();
        $publicadas = \App\Models\Noticia::where('status', 'publicado')->count();
        $programadas = \App\Models\Noticia::where('status', 'programado')->count();
        $borradores = \App\Models\Noticia::where('status', 'borrador')->count();
        $mensajesSinLeer = \App\Models\MensajeContacto::where('leido', false)->count();
        $comentariosPendientes = \App\Models\Comentario::pendientes()->count();
    @endphp

    <main class="panel-admin" id="panelAdmin">

        <header class="admin-page-head">
            <div>
                <span class="admin-eyebrow">Resumen general</span>
                <h1 class="admin-page-head__title">Panel de administración</h1>
                <p class="admin-page-head__subtitle">
                    Publica, revisa y da mantenimiento a los contenidos de UMC News.
                </p>
            </div>

            <div class="admin-page-head__actions">
                <button type="button" class="admin-btn admin-btn--ghost"
                        data-abrir-acertijo aria-haspopup="dialog" aria-controls="modalAcertijoAdmin">
                    <i class="fas fa-key" aria-hidden="true"></i>
                    Acertijo del día
                </button>

                <a href="{{ route('admin.noticias.create') }}" class="admin-btn admin-btn--primary">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    Nueva publicación
                </a>
            </div>
        </header>

        @if(session('message'))
            <div class="alerta-exito" role="status">
                <i class="fas fa-circle-check" aria-hidden="true"></i>
                {{ session('message') }}
            </div>
        @endif

        <section class="admin-stats" aria-label="Métricas de publicaciones">
            <article class="stat-card">
                <span class="stat-card__icon stat-card__icon--navy" aria-hidden="true">
                    <i class="fas fa-newspaper"></i>
                </span>
                <span class="stat-card__value">{{ $totalPublicaciones }}</span>
                <span class="stat-card__label">Publicaciones totales</span>
                <span class="stat-card__hint">Todo el archivo del periódico</span>
            </article>

            <article class="stat-card">
                <span class="stat-card__icon stat-card__icon--green" aria-hidden="true">
                    <i class="fas fa-eye"></i>
                </span>
                <span class="stat-card__value">{{ $publicadas }}</span>
                <span class="stat-card__label">Publicadas</span>
                <span class="stat-card__hint">Visibles en el sitio</span>
            </article>

            <article class="stat-card">
                <span class="stat-card__icon stat-card__icon--gold" aria-hidden="true">
                    <i class="fas fa-clock"></i>
                </span>
                <span class="stat-card__value">{{ $programadas }}</span>
                <span class="stat-card__label">Programadas</span>
                <span class="stat-card__hint">Salen automáticamente</span>
            </article>

            <article class="stat-card">
                <span class="stat-card__icon stat-card__icon--slate" aria-hidden="true">
                    <i class="fas fa-pen-nib"></i>
                </span>
                <span class="stat-card__value">{{ $borradores }}</span>
                <span class="stat-card__label">Borradores</span>
                <span class="stat-card__hint">Pendientes de terminar</span>
            </article>
        </section>

        <section class="admin-attention" aria-label="Pendientes de revisión">
            <div class="admin-attention__head">
                <h2>
                    <i class="fas fa-bell" aria-hidden="true"></i>
                    Requiere tu atención
                </h2>
            </div>

            <div class="admin-attention__grid">
                <a href="{{ route('admin.comentarios.index') }}" class="attention-item">
                    <span class="attention-item__icon attention-item__icon--violet" aria-hidden="true">
                        <i class="fas fa-comments"></i>
                    </span>
                    <span class="attention-item__text">
                        <strong>{{ $comentariosPendientes }}</strong>
                        <small>{{ $comentariosPendientes === 1 ? 'comentario pendiente' : 'comentarios pendientes' }}</small>
                    </span>
                    <i class="fas fa-chevron-right attention-item__arrow" aria-hidden="true"></i>
                </a>

                <a href="{{ route('admin.mensajes.index') }}" class="attention-item">
                    <span class="attention-item__icon attention-item__icon--teal" aria-hidden="true">
                        <i class="fas fa-envelope-open-text"></i>
                    </span>
                    <span class="attention-item__text">
                        <strong>{{ $mensajesSinLeer }}</strong>
                        <small>{{ $mensajesSinLeer === 1 ? 'mensaje sin leer' : 'mensajes sin leer' }}</small>
                    </span>
                    <i class="fas fa-chevron-right attention-item__arrow" aria-hidden="true"></i>
                </a>
            </div>
        </section>

        <!-- Modal para modificar el acertijo del día -->
        <div id="modalAcertijoAdmin" class="modal-acertijo" role="dialog" aria-modal="true"
             aria-labelledby="tituloModalAcertijo" hidden>
            <div class="modal-acertijo__panel">
                <header class="modal-acertijo__header">
                    <div>
                        <span class="modal-acertijo__eyebrow">Soplar al Viento</span>
                        <h3 id="tituloModalAcertijo" class="modal-acertijo__titulo">
                            Acertijo diario
                        </h3>
                    </div>
                    <button type="button" class="modal-acertijo__cerrar" data-cerrar-acertijo
                            aria-label="Cerrar ventana del acertijo">
                        <i class="fas fa-xmark" aria-hidden="true"></i>
                    </button>
                </header>

                <form action="{{ route('admin.acertijo.store') }}" method="POST" class="modal-acertijo__form">
                    @csrf
                    <div class="modal-acertijo__grupo">
                        <label for="acertijoPregunta">Pregunta o acertijo</label>
                        <textarea name="pregunta" id="acertijoPregunta" rows="3" required
                                  placeholder="Escribe el acertijo que verán los lectores...">{{ $acertijoActualAdmin->pregunta ?? '' }}</textarea>
                        <small class="modal-acertijo__ayuda">Aparecerá en la sección Campus del sitio.</small>
                    </div>

                    <div class="modal-acertijo__grupo">
                        <label for="acertijoRespuesta">Respuesta correcta</label>
                        <input type="text" name="respuesta" id="acertijoRespuesta" required
                               value="{{ $acertijoActualAdmin->respuesta ?? '' }}"
                               placeholder="Ej: bitacora">
                    </div>

                    <div class="modal-acertijo__grupo">
                        <label for="acertijoPista">Pista <span>(opcional)</span></label>
                        <input type="text" name="pista" id="acertijoPista"
                               value="{{ $acertijoActualAdmin->pista ?? '' }}"
                               placeholder="Ej: Una sola palabra...">
                    </div>

                    <div class="modal-acertijo__acciones">
                        <button type="button" class="admin-btn admin-btn--ghost" data-cerrar-acertijo>
                            Cancelar
                        </button>
                        <button type="submit" class="admin-btn admin-btn--primary">
                            Guardar y publicar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sección de Noticias Existentes -->
        <section class="panel-admin__seccion">
            <header class="panel-admin__seccion-head">
                <div>
                    <h2 class="panel-admin__seccion-titulo">
                        Publicaciones recientes
                    </h2>
                    <p class="panel-admin__seccion-subtitulo">
                        {{ $totalPublicaciones }} {{ $totalPublicaciones === 1 ? 'noticia registrada' : 'noticias registradas' }}
                    </p>
                </div>

                <a href="{{ route('admin.noticias.auditoria') }}" class="admin-link">
                    Ver historial
                    <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </a>
            </header>

            <div class="grilla-admin" id="listaPublicaciones">
                @forelse($noticias as $noticia)
                    <article class="tarjeta-admin" data-id="{{ $noticia->id_noticia }}">
                        <div class="tarjeta-admin__imagen-wrapper">
                            @if($noticia->imagen_noticia)
                                <img src="{{ asset('storage/' . $noticia->imagen_noticia) }}" alt="{{ $noticia->titulo }}" class="tarjeta-admin__imagen">
                            @else
                                <div class="tarjeta-admin__sin-imagen">
                                    <i class="fas fa-image" aria-hidden="true"></i>
                                    <span>Sin imagen</span>
                                </div>
                            @endif

                            @if($noticia->es_principal)
                                <span class="badge-principal">
                                    <i class="fas fa-star" aria-hidden="true"></i> Principal
                                </span>
                            @endif
                        </div>

                        <div class="tarjeta-admin__contenido">
                            <div class="tarjeta-admin__meta">
                                <span class="badge-categoria">
                                    {{ $noticia->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                                <span class="badge-status badge-status--{{ $noticia->status ?? 'borrador' }}">
                                    {{ ucfirst($noticia->status ?? 'Borrador') }}
                                </span>
                            </div>

                            <h3 class="tarjeta-admin__titulo">{{ $noticia->titulo }}</h3>

                            <p class="tarjeta-admin__extracto">
                                {{ Str::limit(strip_tags($noticia->contenido), 90, '...') }}
                            </p>

                            @if($noticia->status === 'programado' && $noticia->programada_para)
                                <p class="tarjeta-admin__programacion">
                                    <i class="fas fa-clock" aria-hidden="true"></i>
                                    Se publicará el {{ $noticia->programada_para->format('d/m/Y h:i A') }}
                                </p>
                            @endif

                            <p class="tarjeta-admin__fechas">
                                Actualizada: {{ $noticia->updated_at?->format('d/m/Y h:i A') ?? '—' }}
                            </p>

                            <footer class="tarjeta-admin__footer">
                                <a href="{{ route('admin.noticias.edit', $noticia->id_noticia) }}" class="btn-editar">
                                    <i class="fas fa-pen-to-square" aria-hidden="true"></i> Editar
                                </a>

                                <form action="{{ route('admin.noticias.destroy', $noticia->id_noticia) }}" method="POST"
                                      onsubmit="return confirm('¿Estás seguro de eliminar esta noticia?');" class="form-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-eliminar" title="Eliminar noticia">
                                        <i class="fas fa-trash-can" aria-hidden="true"></i>
                                        <span>Eliminar</span>
                                    </button>
                                </form>
                            </footer>
                        </div>
                    </article>
                @empty
                    <div class="sin-noticias">
                        <i class="fas fa-folder-open" aria-hidden="true"></i>
                        <p>No hay noticias registradas todavía.</p>
                        <a href="{{ route('admin.noticias.create') }}" class="admin-btn admin-btn--primary">
                            Crear la primera publicación
                        </a>
                    </div>
                @endforelse
            </div>

            <div class="paginacion-contenedor">
                {{ $noticias->links('pagination::bootstrap-5') }}
            </div>
        </section>

    </main>

    <script src="{{ asset('js/admin-acertijo-modal.js') }}" defer></script>

</x-admin-layout>
