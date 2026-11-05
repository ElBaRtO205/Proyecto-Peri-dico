<x-admin-layout>

    <main class="panel-admin" id="panelAdmin">
        <header class="admin-page-head">
            <div>
                <span class="admin-eyebrow">Buzón</span>
                <h1 class="admin-page-head__title">Mensajes de contacto</h1>
                <p class="admin-page-head__subtitle">
                    Sugerencias, mensajes y recomendaciones para el podcast enviadas por la comunidad.
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
                        Bandeja de entrada
                        <span class="contador">({{ $mensajes->total() }})</span>
                    </h2>
                    <p class="panel-admin__seccion-subtitulo">
                        @if($noLeidos > 0)
                            {{ $noLeidos }} {{ $noLeidos === 1 ? 'mensaje sin leer' : 'mensajes sin leer' }}
                        @else
                            Todo al día: no hay mensajes sin leer.
                        @endif
                    </p>
                </div>
            </header>

            <div class="admin-list">
                @forelse($mensajes as $mensaje)
                    <article class="admin-list-card {{ $mensaje->leido ? 'admin-list-card--read' : 'admin-list-card--unread' }}">
                        <div class="admin-list-card__head">
                            <div class="admin-list-card__who">
                                <span class="admin-pill admin-pill--{{ $mensaje->tipo }}">
                                    {{ $mensaje->tipo_label }}
                                </span>
                                <strong>{{ $mensaje->nombre }}</strong>
                                <span>&lt;{{ $mensaje->email }}&gt;</span>
                            </div>

                            <div class="admin-list-card__who">
                                <time class="admin-list-card__time" datetime="{{ $mensaje->created_at->toIso8601String() }}">
                                    {{ $mensaje->created_at->format('d/m/Y h:i A') }}
                                </time>
                                @if(! $mensaje->leido)
                                    <span class="admin-pill admin-pill--nuevo">Nuevo</span>
                                @endif
                            </div>
                        </div>

                        @if($mensaje->asunto)
                            <p class="admin-list-card__body">
                                <strong>Asunto:</strong> {{ $mensaje->asunto }}
                            </p>
                        @endif

                        @if($mensaje->tipo === 'podcast')
                            <div class="admin-list-card__note">
                                <p><strong>Persona recomendada:</strong> {{ $mensaje->persona_recomendada }}</p>
                                @if($mensaje->enlace_persona)
                                    <p><strong>Enlace o red social:</strong> {{ $mensaje->enlace_persona }}</p>
                                @endif
                            </div>
                        @endif

                        <div class="admin-list-card__body">
                            {!! nl2br(e($mensaje->mensaje)) !!}
                        </div>

                        <div class="admin-list-card__actions">
                            <form action="{{ route('admin.mensajes.leido', $mensaje) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">
                                    <i class="fas fa-{{ $mensaje->leido ? 'envelope' : 'envelope-open' }}" aria-hidden="true"></i>
                                    {{ $mensaje->leido ? 'Marcar como no leído' : 'Marcar como leído' }}
                                </button>
                            </form>

                            <a href="mailto:{{ $mensaje->email }}" class="admin-btn admin-btn--ghost admin-btn--sm">
                                <i class="fas fa-reply" aria-hidden="true"></i> Responder
                            </a>

                            <form action="{{ route('admin.mensajes.destroy', $mensaje) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar este mensaje?');">
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
                        <i class="fas fa-inbox" aria-hidden="true"></i>
                        <p>No hay mensajes de contacto todavía.</p>
                    </div>
                @endforelse
            </div>

            <div class="paginacion-contenedor">
                {{ $mensajes->links('pagination::bootstrap-5') }}
            </div>
        </section>
    </main>

</x-admin-layout>
