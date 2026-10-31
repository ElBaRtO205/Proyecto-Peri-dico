<x-admin-layout>

    <main class="panel-admin" id="panelAdmin">
        <header class="panel-admin__header">
            <div>
                <h1 class="panel-admin__titulo">
                    <i class="fas fa-inbox"></i> Mensajes de contacto
                </h1>
                <p class="panel-admin__subtitulo">
                    Buzón de sugerencias, mensajes y recomendaciones para el podcast
                </p>
            </div>

            <a href="{{ route('admin.noticias.index') }}" class="panel-admin__btn-salir" style="text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Volver al panel
            </a>
        </header>

        @if(session('message'))
            <div class="alerta-exito">
                <i class="fas fa-check-circle"></i> {{ session('message') }}
            </div>
        @endif

        <section class="panel-admin__seccion">
            <h2 class="panel-admin__seccion-titulo">
                <i class="fas fa-envelope-open-text"></i> Bandeja de entrada
                <span class="contador">({{ $mensajes->total() }})</span>
                @if($noLeidos > 0)
                    <span style="margin-left: 10px; padding: 3px 9px; border-radius: 999px; background: #f59e0b; color: #1f2937; font-size: 0.75rem;">
                        {{ $noLeidos }} sin leer
                    </span>
                @endif
            </h2>

            @forelse($mensajes as $mensaje)
                <article style="margin-bottom: 16px; padding: 20px; border: 1px solid {{ $mensaje->leido ? '#334155' : '#f59e0b' }}; border-left: 5px solid {{ $mensaje->leido ? '#334155' : '#f59e0b' }}; border-radius: 12px; background: #1e293b; color: #e2e8f0;">
                    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 12px;">
                        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
                            <span style="padding: 4px 10px; border-radius: 999px; background: {{ $mensaje->tipo === 'podcast' ? '#7c3aed' : ($mensaje->tipo === 'contacto' ? '#0284c7' : '#0f766e') }}; color: #ffffff; font-size: 0.75rem; font-weight: 700;">
                                {{ $mensaje->tipo_label }}
                            </span>
                            <strong style="color: #f8fafc;">{{ $mensaje->nombre }}</strong>
                            <span style="color: #94a3b8;">&lt;{{ $mensaje->email }}&gt;</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: #94a3b8;">
                            <time datetime="{{ $mensaje->created_at->toIso8601String() }}">{{ $mensaje->created_at->format('d/m/Y h:i A') }}</time>
                            @if(! $mensaje->leido)
                                <span style="padding: 3px 8px; border-radius: 999px; background: #f59e0b; color: #1f2937; font-weight: 700;">Nuevo</span>
                            @endif
                        </div>
                    </div>

                    @if($mensaje->asunto)
                        <p style="margin: 0 0 8px; color: #cbd5e1;"><strong>Asunto:</strong> {{ $mensaje->asunto }}</p>
                    @endif

                    @if($mensaje->tipo === 'podcast')
                        <div style="margin-bottom: 10px; padding: 10px 12px; border-radius: 8px; background: rgba(124, 58, 237, 0.15); border: 1px solid rgba(124, 58, 237, 0.35);">
                            <p style="margin: 0 0 4px;"><strong>Persona recomendada:</strong> {{ $mensaje->persona_recomendada }}</p>
                            @if($mensaje->enlace_persona)
                                <p style="margin: 0; color: #c4b5fd;"><strong>Enlace / red social:</strong> {{ $mensaje->enlace_persona }}</p>
                            @endif
                        </div>
                    @endif

                    <p style="margin: 0 0 14px; line-height: 1.6; color: #e2e8f0;">{!! nl2br(e($mensaje->mensaje)) !!}</p>

                    <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                        <form action="{{ route('admin.mensajes.leido', $mensaje) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" style="padding: 7px 12px; border: 1px solid #475569; border-radius: 7px; background: transparent; color: #cbd5e1; cursor: pointer;">
                                <i class="fas fa-{{ $mensaje->leido ? 'envelope' : 'envelope-open' }}"></i>
                                {{ $mensaje->leido ? 'Marcar como no leído' : 'Marcar como leído' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.mensajes.destroy', $mensaje) }}" method="POST" onsubmit="return confirm('¿Eliminar este mensaje?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="padding: 7px 12px; border: 1px solid #7f1d1d; border-radius: 7px; background: rgba(127, 29, 29, 0.25); color: #fca5a5; cursor: pointer;">
                                <i class="fas fa-trash-can"></i> Eliminar
                            </button>
                        </form>

                        <a href="mailto:{{ $mensaje->email }}" style="padding: 7px 12px; border: 1px solid #475569; border-radius: 7px; color: #cbd5e1; text-decoration: none;">
                            <i class="fas fa-reply"></i> Responder
                        </a>
                    </div>
                </article>
            @empty
                <div class="sin-noticias">
                    <i class="fas fa-inbox"></i>
                    <p>No hay mensajes de contacto todavía.</p>
                </div>
            @endforelse

            <div class="paginacion-contenedor">
                {{ $mensajes->links('pagination::bootstrap-5') }}
            </div>
        </section>
    </main>

</x-admin-layout>
