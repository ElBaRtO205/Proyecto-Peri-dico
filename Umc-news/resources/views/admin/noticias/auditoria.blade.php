<x-admin-layout>

    <main class="panel-admin" id="panelAdmin">
        <header class="admin-page-head">
            <div>
                <span class="admin-eyebrow">Auditoría</span>
                <h1 class="admin-page-head__title">Historial de cambios</h1>
                <p class="admin-page-head__subtitle">
                    Registro de creaciones, ediciones y eliminaciones de noticias.
                </p>
            </div>

            <div class="admin-page-head__actions">
                <a href="{{ route('admin.noticias.index') }}" class="admin-btn admin-btn--ghost">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    Volver al panel
                </a>
            </div>
        </header>

        <section class="panel-admin__seccion">
            <header class="panel-admin__seccion-head">
                <div>
                    <h2 class="panel-admin__seccion-titulo">
                        Actividad reciente
                        <span class="contador">({{ $registros->total() }})</span>
                    </h2>
                    <p class="panel-admin__seccion-subtitulo">
                        Cada cambio queda asociado al usuario que lo realizó.
                    </p>
                </div>
            </header>

            <div class="admin-filters" role="group" aria-label="Filtrar historial por acción">
                @php
                    $filtros = [
                        'todas' => ['label' => 'Todas', 'clase' => 'todos'],
                        'creado' => ['label' => 'Creadas', 'clase' => 'aprobado'],
                        'actualizado' => ['label' => 'Actualizadas', 'clase' => 'actualizado'],
                        'eliminado' => ['label' => 'Eliminadas', 'clase' => 'rechazado'],
                    ];
                @endphp

                @foreach($filtros as $clave => $filtro)
                    <a href="{{ route('admin.noticias.auditoria', ['accion' => $clave]) }}"
                       class="admin-chip admin-chip--{{ $filtro['clase'] }} {{ $accion === $clave ? 'is-active' : '' }}">
                        {{ $filtro['label'] }} ({{ $conteo[$clave] }})
                    </a>
                @endforeach
            </div>

            <div class="admin-list">
                @forelse($registros as $registro)
                    <article class="admin-list-card admin-list-card--{{ $registro->accion }}">
                        <div class="admin-list-card__head">
                            <div class="admin-list-card__who">
                                <span class="admin-pill admin-pill--{{ $registro->accion }}">
                                    {{ $registro->accion }}
                                </span>
                                <strong>{{ $registro->usuario?->nombre_completo ?? 'Sistema' }}</strong>
                            </div>

                            <time class="admin-list-card__time" datetime="{{ $registro->created_at?->toIso8601String() }}">
                                {{ $registro->created_at?->format('d/m/Y h:i A') }}
                            </time>
                        </div>

                        <p class="admin-list-card__body">
                            @if($registro->noticia)
                                <a href="{{ route('admin.noticias.edit', $registro->noticia->id_noticia) }}">
                                    {{ $registro->titulo ?? $registro->noticia->titulo }}
                                </a>
                            @else
                                {{ $registro->titulo ?? 'Noticia eliminada' }}
                                <span class="admin-muted">(noticia eliminada)</span>
                            @endif
                        </p>

                        @if(! empty($registro->cambios['despues']) && is_array($registro->cambios['despues']))
                            <p class="admin-muted">
                                Campos modificados:
                                {{ implode(', ', array_keys($registro->cambios['despues'])) }}
                            </p>
                        @endif
                    </article>
                @empty
                    <div class="sin-noticias">
                        <i class="fas fa-clock-rotate-left" aria-hidden="true"></i>
                        <p>No hay registros de auditoría con este filtro.</p>
                    </div>
                @endforelse
            </div>

            <div class="paginacion-contenedor">
                {{ $registros->links('pagination::bootstrap-5') }}
            </div>
        </section>
    </main>

</x-admin-layout>
