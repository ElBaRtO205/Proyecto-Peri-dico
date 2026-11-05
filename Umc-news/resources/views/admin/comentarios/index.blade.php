<x-admin-layout>

    <main class="panel-admin" id="panelAdmin">
        <header class="admin-page-head">
            <div>
                <span class="admin-eyebrow">Moderación</span>
                <h1 class="admin-page-head__title">Comentarios</h1>
                <p class="admin-page-head__subtitle">
                    Revisa, aprueba o rechaza los comentarios que envían los lectores.
                </p>
            </div>

            <div class="admin-page-head__actions">
                <a href="{{ route('admin.noticias.index') }}" class="admin-btn admin-btn--ghost">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Volver al panel
                </a>
            </div>
        </header>

        @if(session('message'))
            <div class="alerta-exito" role="status">
                <i class="fas fa-circle-check" aria-hidden="true"></i>
                {{ session('message') }}
            </div>
        @endif

        <section class="panel-admin__seccion">
            <header class="panel-admin__seccion-head">
                <div>
                    <h2 class="panel-admin__seccion-titulo">
                        Bandeja de comentarios
                        <span class="contador">({{ $comentarios->total() }})</span>
                    </h2>
                    <p class="panel-admin__seccion-subtitulo">
                        Filtra por estado para encontrar lo que necesita revisión.
                    </p>
                </div>
            </header>

            <div class="admin-filters" role="group" aria-label="Filtrar comentarios por estado">
                @php
                    $filtros = [
                        'pendiente' => ['label' => 'Pendientes', 'clase' => 'pendiente'],
                        'aprobado' => ['label' => 'Aprobados', 'clase' => 'aprobado'],
                        'rechazado' => ['label' => 'Rechazados', 'clase' => 'rechazado'],
                        'todos' => ['label' => 'Todos', 'clase' => 'todos'],
                    ];
                @endphp

                @foreach($filtros as $clave => $filtro)
                    <a href="{{ route('admin.comentarios.index', ['status' => $clave]) }}"
                       class="admin-chip admin-chip--{{ $filtro['clase'] }} {{ $status === $clave ? 'is-active' : '' }}">
                        {{ $filtro['label'] }}
                        ({{ $clave === 'todos' ? $conteo['todos'] : $conteo[$clave === 'pendiente' ? 'pendientes' : ($clave === 'aprobado' ? 'aprobados' : 'rechazados')] }})
                    </a>
                @endforeach
            </div>

            <div class="admin-list">
                @forelse($comentarios as $comentario)
                    <article class="admin-list-card admin-list-card--{{ $comentario->status }}">
                        <div class="admin-list-card__head">
                            <div class="admin-list-card__who">
                                <span class="admin-pill admin-pill--{{ $comentario->status }}">
                                    {{ $comentario->status }}
                                </span>
                                <strong>{{ $comentario->usuario?->nombre_completo ?? $comentario->autor }}</strong>
                                @if($comentario->usuario)
                                    <span>&lt;{{ $comentario->usuario->email }}&gt;</span>
                                @endif
                            </div>

                            <time class="admin-list-card__time" datetime="{{ $comentario->created_at->toIso8601String() }}">
                                {{ $comentario->created_at->format('d/m/Y h:i A') }}
                            </time>
                        </div>

                        <p class="admin-list-card__body admin-muted">
                            En la noticia:
                            @if($comentario->noticia)
                                <a href="{{ route('noticia.show', $comentario->noticia) }}" target="_blank" rel="noopener">
                                    {{ $comentario->noticia->titulo }}
                                </a>
                            @else
                                Noticia eliminada
                            @endif
                        </p>

                        <div class="admin-list-card__body">
                            {{ $comentario->contenido }}
                        </div>

                        <div class="admin-list-card__actions">
                            @if($comentario->status !== 'aprobado')
                                <form action="{{ route('admin.comentarios.aprobar', $comentario) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">
                                        <i class="fas fa-check" aria-hidden="true"></i> Aprobar
                                    </button>
                                </form>
                            @endif

                            @if($comentario->status !== 'rechazado')
                                <form action="{{ route('admin.comentarios.rechazar', $comentario) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">
                                        <i class="fas fa-ban" aria-hidden="true"></i> Rechazar
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('admin.comentarios.destroy', $comentario) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar este comentario definitivamente?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">
                                    <i class="fas fa-trash-can" aria-hidden="true"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="sin-noticias">
                        <i class="fas fa-comment-slash" aria-hidden="true"></i>
                        <p>No hay comentarios con este estado.</p>
                    </div>
                @endforelse
            </div>

            <div class="paginacion-contenedor">
                {{ $comentarios->links('pagination::bootstrap-5') }}
            </div>
        </section>
    </main>

</x-admin-layout>
