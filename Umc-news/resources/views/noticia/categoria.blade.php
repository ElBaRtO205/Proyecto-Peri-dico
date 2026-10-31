<x-app-layout>
    <x-slot:title>UMC News | {{ $categoria->nombre }}</x-slot:title>

    <div class="categoria-container">
        <!-- Botón para regresar -->
        <div class="categoria-header-nav">
            <a href="{{ route('home') }}" class="btn-volver">
                <i class="fas fa-arrow-left"></i> Volver al Inicio
            </a>
        </div>

        <!-- Título de la sección -->
        <header class="categoria-titulo-bloque">
            <h1 class="categoria-titulo">Sección: {{ $categoria->nombre }}</h1>

            {{-- Solo aparece si internamente la categoría se llama Campus --}}
        @if($categoria->nombre === 'Campus')
           @php
    $acertijoHoy = \App\Models\Acertijo::where('activo', true)->orderByDesc('id_acertijo')->first();
@endphp

            @if($acertijoHoy)
                <div class="susurro-acertijo-banner">
                    <div class="susurro-acertijo__icono" id="btnAbrirAcertijoCat" title="Hay un murmullo en el campus...">
                        <i class="fas fa-wind"></i>
                    </div>
                    <div id="contenedorAcertijoCat" class="susurro-acertijo__cuerpo" style="display: none;">
                        <span class="susurro-acertijo__etiqueta">El Acertijo del Campus</span>
                        <p class="susurro-acertijo__pregunta">"{{ $acertijoHoy->pregunta }}"</p>
                       @php
    $acertijoHoy = \App\Models\Acertijo::where('activo', true)->orderByDesc('id_acertijo')->first();
@endphp
                        <div class="susurro-acertijo__form">
                            <input type="text" id="inputClaveCat" placeholder="Susurra la respuesta...">
                            <button type="button" id="btnEnviarCat">Soplar</button>
                        </div>
                        <p id="msgAcertijoCat" class="susurro-acertijo__msg"></p>
                    </div>
                </div>

                <style>
                    .susurro-acertijo-banner {
                        max-width: 700px;
                        margin: 0 auto 2rem;
                        text-align: center;
                    }
                    .susurro-acertijo__icono {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        width: 42px;
                        height: 42px;
                        border-radius: 50%;
                        background: rgba(15, 23, 42, 0.08);
                        color: #64748b;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    }
                    .susurro-acertijo__icono:hover {
                        background: #0f172a;
                        color: #38bdf8;
                        transform: rotate(15deg) scale(1.1);
                    }
                    .susurro-acertijo__cuerpo {
                        margin-top: 12px;
                        padding: 1.25rem;
                        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                        color: #e2e8f0;
                        border-radius: 12px;
                        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
                        border: 1px solid rgba(56, 189, 248, 0.25);
                    }
                    .susurro-acertijo__etiqueta {
                        font-size: 0.72rem;
                        text-transform: uppercase;
                        letter-spacing: 2px;
                        color: #38bdf8;
                        display: block;
                        margin-bottom: 6px;
                    }
                    .susurro-acertijo__pregunta {
                        font-style: italic;
                        font-size: 1rem;
                        margin: 0.5rem 0;
                    }
                    .susurro-acertijo__pista {
                        display: block;
                        color: #94a3b8;
                        margin-bottom: 10px;
                    }
                    .susurro-acertijo__form {
                        display: flex;
                        gap: 8px;
                        max-width: 400px;
                        margin: 10px auto 0;
                    }
                    .susurro-acertijo__form input {
                        flex: 1;
                        padding: 8px 12px;
                        border-radius: 6px;
                        border: 1px solid #334155;
                        background: #090d16;
                        color: #fff;
                    }
                    .susurro-acertijo__form button {
                        padding: 8px 16px;
                        border-radius: 6px;
                        border: none;
                        background: #0284c7;
                        color: #fff;
                        cursor: pointer;
                        font-weight: 600;
                    }
                    .susurro-acertijo__msg {
                        margin-top: 8px;
                        font-size: 0.85rem;
                        display: none;
                    }
                </style>

                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const btnAbrir = document.getElementById('btnAbrirAcertijoCat');
                        const contenedor = document.getElementById('contenedorAcertijoCat');
                        const btnEnviar = document.getElementById('btnEnviarCat');
                        const input = document.getElementById('inputClaveCat');
                        const msg = document.getElementById('msgAcertijoCat');

                        btnAbrir?.addEventListener('click', () => {
                            contenedor.style.display = contenedor.style.display === 'none' ? 'block' : 'none';
                            if (contenedor.style.display === 'block') input.focus();
                        });

                        const enviarRespuesta = async () => {
                            if (!input.value.trim()) return;
                            const res = await fetch("{{ route('secreto.desbloquear') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ clave: input.value })
                            });
                            const data = await res.json();
                            msg.style.display = 'block';
                            msg.textContent = data.mensaje;
                            msg.style.color = data.exito ? '#38bdf8' : '#f87171';
                        };

                        btnEnviar?.addEventListener('click', enviarRespuesta);
                        input?.addEventListener('keypress', (e) => { if (e.key === 'Enter') enviarRespuesta(); });
                    });
                </script>
            @endif
        @endif
        </header>

        <!-- Grilla de noticias en versión reducida -->
        @if($noticias->count() > 0)
            <div class="grilla-noticias grilla-noticias--categoria">
                @foreach($noticias as $noticia)
                    <article class="tarjeta-noticia tarjeta-noticia--compacta">
                        <a href="{{ route('noticia.show', $noticia) }}" class="tarjeta-noticia__vista-previa">
                            
                            <img class="tarjeta-noticia__imagen" 
                                 src="{{ $noticia->imagen_noticia ? asset('storage/' . $noticia->imagen_noticia) : asset('img/placeholder-noticia.svg') }}" 
                                 alt="{{ $noticia->titulo }}" 
                                 loading="lazy">
                            
                            <div class="tarjeta-noticia__contenido">
                                <h2 class="tarjeta-noticia__titulo">{{ $noticia->titulo }}</h2>
                                <p class="tarjeta-noticia__texto">{{ Str::limit($noticia->contenido, 110) }}</p>
                                
                                <div class="tarjeta-noticia__meta">
                                    <span class="tarjeta-noticia__autor">{{ $noticia->autor ?: 'Redacción UMC' }}</span>
                                    @if($noticia->fecha_publicacion)
                                        <time class="tarjeta-noticia__fecha" datetime="{{ $noticia->fecha_publicacion->format('Y-m-d') }}">
                                            {{ $noticia->fecha_publicacion->format('d/m/Y') }}
                                        </time>
                                    @endif
                                </div>
                            </div>

                        </a>
                    </article>
                @endforeach
            </div>

            <!-- Paginación de Laravel -->
            <div class="categoria-paginacion">
                {{ $noticias->links() }}
            </div>
        @else
            <div class="categoria-vacia">
                <p><i class="fas fa-info-circle"></i> No hay noticias publicadas en esta sección todavía.</p>
            </div>
        @endif
    </div>
</x-app-layout>