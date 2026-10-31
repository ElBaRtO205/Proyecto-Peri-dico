<x-admin-layout>

    
    <main class="panel-admin" id="panelAdmin">

        <!-- Encabezado del Panel -->
        <header class="panel-admin__header">
            <div>
                <h1 class="panel-admin__titulo">
                    <i class="fas fa-gauge-high"></i> Panel de Administración
                </h1>
                <p class="panel-admin__subtitulo">Gestión de contenidos — UMC News</p>
            </div>
            
            <form action="{{ route('logout') }}" method="POST" class="panel-admin__logout-form">
                @csrf
                <button type="submit" class="panel-admin__btn-salir">
                    <i class="fas fa-right-from-bracket"></i> Salir del Modo Admin
                </button>
            </form>
        </header>

        <!-- Alerta de Éxito -->
        @if(session('message'))
            <div class="alerta-exito">
                <i class="fas fa-check-circle"></i> {{ session('message') }}
            </div>
        @endif

       @php
            $acertijoActualAdmin = \App\Models\Acertijo::where('activo', true)->orderByDesc('id_acertijo')->first();
        @endphp

        <!-- Botones de Acción del Panel -->
        <section class="panel-admin__acciones" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
            <a href="{{ route('admin.noticias.create') }}" class="panel-admin__btn-nueva">
                <i class="fas fa-plus"></i> Nueva Publicación
            </a>

            <button type="button" onclick="document.getElementById('modalAcertijoAdmin').style.display='flex'" 
                    class="panel-admin__btn-nueva" style="background: #0284c7; border: none; cursor: pointer;">
                <i class="fas fa-wind"></i> Modificar Acertijo del Día
            </button>

            <a href="{{ route('secreto.index') }}" class="panel-admin__btn-nueva panel-admin__btn-nueva--claro" style="background: #334155;">
                <i class="fas fa-mask"></i> Ver "Soplar al Viento"
            </a>

            <a href="{{ route('admin.mensajes.index') }}" class="panel-admin__btn-nueva panel-admin__btn-nueva--claro" style="background: #0f766e;">
                <i class="fas fa-inbox"></i> Mensajes
                ({{ \App\Models\MensajeContacto::where('leido', false)->count() }})
            </a>
        </section>

        <!-- Modal para Modificar el Acertijo desde el Panel Admin -->
        <div id="modalAcertijoAdmin" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.65); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
            <div style="background: #1e293b; color: #f8fafc; width: 100%; max-width: 520px; border-radius: 14px; padding: 1.5rem; box-shadow: 0 20px 40px rgba(0,0,0,0.4); border: 1px solid rgba(56,189,248,0.3);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.75rem;">
                    <h3 style="margin: 0; font-size: 1.15rem; color: #38bdf8;">
                        <i class="fas fa-key"></i> Acertijo Diario — Soplar al Viento
                    </h3>
                    <button type="button" onclick="document.getElementById('modalAcertijoAdmin').style.display='none'" 
                            style="background: transparent; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer;">✕</button>
                </div>

                <form action="{{ route('admin.acertijo.store') }}" method="POST">
                    @csrf
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 0.85rem; margin-bottom: 6px; color: #cbd5e1;">Pregunta / Acertijo (Aparecerá en Rumores del Campus):</label>
                        <textarea name="pregunta" rows="3" required 
                                  style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #475569; background: #0f172a; color: #fff; box-sizing: border-box;"
                                  placeholder="Escribe el acertijo aquí...">{{ $acertijoActualAdmin->pregunta ?? '' }}</textarea>
                    </div>

                    <div style="margin-bottom: 12px;">
                        <label style="display: block; font-size: 0.85rem; margin-bottom: 6px; color: #cbd5e1;">Respuesta correcta (Palabra clave):</label>
                        <input type="text" name="respuesta" required value="{{ $acertijoActualAdmin->respuesta ?? '' }}"
                               style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #475569; background: #0f172a; color: #fff; box-sizing: border-box;"
                               placeholder="Ej: bitacora">
                    </div>

                    <div style="margin-bottom: 18px;">
                        <label style="display: block; font-size: 0.85rem; margin-bottom: 6px; color: #cbd5e1;">Pista opcional:</label>
                        <input type="text" name="pista" value="{{ $acertijoActualAdmin->pista ?? '' }}"
                               style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #475569; background: #0f172a; color: #fff; box-sizing: border-box;"
                               placeholder="Ej: Una sola palabra...">
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 10px;">
                        <button type="button" onclick="document.getElementById('modalAcertijoAdmin').style.display='none'"
                                style="padding: 9px 16px; border-radius: 8px; border: 1px solid #475569; background: transparent; color: #cbd5e1; cursor: pointer;">
                            Cancelar
                        </button>
                        <button type="submit" 
                                style="padding: 9px 18px; border-radius: 8px; border: none; background: #0284c7; color: #fff; font-weight: 600; cursor: pointer;">
                            Guardar y Publicar Acertijo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sección de Noticias Existentes -->
        <section class="panel-admin__seccion">
            <h2 class="panel-admin__seccion-titulo">
                <i class="fas fa-newspaper"></i> Publicaciones Existentes
                <span class="contador" id="contadorPublicaciones">({{ $noticias->total() }})</span>
            </h2>
            
            <div class="grilla-admin" id="listaPublicaciones">
                @forelse($noticias as $noticia)
                    <article class="tarjeta-admin" data-id="{{ $noticia->id_noticia }}">
                        
                        <!-- Imagen + Badge Principal -->
                        <div class="tarjeta-admin__imagen-wrapper">
                            @if($noticia->imagen_noticia)
                                <img src="{{ asset('storage/' . $noticia->imagen_noticia) }}" alt="{{ $noticia->titulo }}" class="tarjeta-admin__imagen">
                            @else
                                <div class="tarjeta-admin__sin-imagen">
                                    <i class="fas fa-image"></i> Sin imagen
                                </div>
                            @endif

                            @if($noticia->es_principal)
                                <span class="badge-principal">★ Principal</span>
                            @endif
                        </div>
                        
                        <div class="tarjeta-admin__contenido">
                            <!-- Metadatos (Categoría y Status) -->
                            <div class="tarjeta-admin__meta">
                                <span class="badge-categoria">
                                    {{ $noticia->categoria->nombre ?? 'Sin categoría' }}
                                </span>
                                <span class="badge-status badge-status--{{ $noticia->status ?? 'borrador' }}">
                                    {{ ucfirst($noticia->status ?? 'Borrador') }}
                                </span>
                            </div>

                            <h3 class="tarjeta-admin__titulo">{{ $noticia->titulo }}</h3>

                            <!-- Recorte de texto a 90 caracteres -->
                            <p class="tarjeta-admin__extracto">
                                {{ Str::limit(strip_tags($noticia->contenido), 90, '...') }}
                            </p>

                            <!-- Acciones -->
                            <footer class="tarjeta-admin__footer">
                                <a href="{{ route('admin.noticias.edit', $noticia->id_noticia) }}" class="btn-editar">
                                    <i class="fas fa-pen-to-square"></i> Editar
                                </a>

                                <form action="{{ route('admin.noticias.destroy', $noticia->id_noticia) }}" method="POST" 
                                      onsubmit="return confirm('¿Estás seguro de eliminar esta noticia?');" class="form-eliminar"> 
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-eliminar"> 
                                        <i class="fas fa-trash-can"></i> Eliminar
                                    </button>
                                </form>
                            </footer>
                        </div>
                    </article>
                @empty
                    <div class="sin-noticias">
                        <i class="fas fa-folder-open"></i>
                        <p>No hay noticias registradas aún.</p>
                    </div>
                @endforelse
            </div>

            <!-- Contenedor de Paginación corregida -->
            <div class="paginacion-contenedor">
                {{ $noticias->links('pagination::bootstrap-5') }}
            </div>
        </section>

    </main>

</x-admin-layout>