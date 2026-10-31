<x-app-layout>
    <x-slot:title>Soplar al Viento | Rincón Secreto UMC</x-slot:title>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Lora:ital,wght@0,400;0,500;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <div class="viento-wrapper">
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

            <!-- ==================== PANEL EXCLUSIVO DEL ADMIN ==================== -->
            @auth
                @if(auth()->user()->rol === 'admin' || auth()->id() === 1) {{-- Ajusta según tu validación de Admin --}}
                
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

                    <textarea name="contenido" rows="3" maxlength="500" required
                              placeholder="Escribe aquello que solo le contarías al viento..."
                              oninput="document.getElementById('contadorLetras').textContent = this.value.length + '/500'"></textarea>

                    <div class="compositor-bottom">
                        <span id="contadorLetras" class="contador-letras">0/500</span>
                        <button type="submit" class="btn-soplar">
                            <i class="fas fa-paper-plane"></i> Lanzar al viento
                        </button>
                    </div>
                </form>
            </section>

            <!-- ==================== FILTROS RÁPIDOS ==================== -->
            <div class="viento-filtros">
                <button class="filtro-btn activo" onclick="filtrarSusurros('Todos', this)">Todos los murmullos</button>
                <button class="filtro-btn" onclick="filtrarSusurros('Confesión', this)">🌊 Confesiones</button>
                <button class="filtro-btn" onclick="filtrarSusurros('Rumor', this)">👀 Rumores</button>
                <button class="filtro-btn" onclick="filtrarSusurros('Desahogo', this)">⛈️ Desahogos</button>
                <button class="filtro-btn" onclick="filtrarSusurros('Verso', this)">📜 Versos</button>
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
                                        onclick="darAliento({{ $susurro->id_susurro }}, this)"
                                        title="Dar aliento a este susurro">
                                    <i class="fas fa-wind"></i> 
                                    <span class="conteo-soplos">{{ $susurro->soplos }}</span>
                                </button>

                                @auth
                                   @if(auth()->user()?->isAdmin())
    <form action="{{ route('admin.susurro.destroy', $susurro->id_susurro) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn-borrar-susurro" title="Disipar (Borrar)">✕</button>
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

    <style>
        .viento-wrapper {
            min-height: 100vh;
            background: radial-gradient(circle at top, #111c2d 0%, #070b12 100%);
            color: #e2e8f0;
            font-family: 'Inter', sans-serif;
            position: relative;
            overflow: hidden;
            padding: 2rem 1rem 5rem;
        }
        /* Líneas sutiles animadas simulando corrientes de aire */
        .viento-bg-lines .linea-viento {
            position: absolute;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(56, 189, 248, 0.25), transparent);
            width: 45%;
            pointer-events: none;
            animation: brisaFlujo 12s linear infinite;
        }
        .linea-viento.l1 { top: 18%; left: -50%; animation-duration: 14s; }
        .linea-viento.l2 { top: 45%; left: -50%; animation-duration: 19s; animation-delay: 3s; }
        .linea-viento.l3 { top: 75%; left: -50%; animation-duration: 16s; animation-delay: 6s; }

        @keyframes brisaFlujo {
            0% { transform: translateX(0); opacity: 0; }
            50% { opacity: 1; }
            100% { transform: translateX(320%); opacity: 0; }
        }

        .viento-container {
            max-width: 1050px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        .viento-top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .viento-nav__volver {
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.25rem;
        }
        .btn-regresar-portada {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.2s;
        }
        .btn-regresar-portada:hover { color: #38bdf8; }
        /* Botón destacado para volver al panel de administración */
        .btn-panel-admin {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: #0f2440;
            background: #c9a227;
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s, transform 0.2s;
        }
        .btn-panel-admin:hover {
            background: #f0c842;
            transform: translateY(-1px);
        }
        .viento-estado {
            font-size: 0.8rem;
            color: #64748b;
            font-style: italic;
        }

        .viento-header {
            text-align: center;
            margin: 1rem auto 2.5rem;
            max-width: 680px;
        }
        .viento-icono-central {
            width: 56px;
            height: 56px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            background: rgba(56, 189, 248, 0.1);
            border: 1px solid rgba(56, 189, 248, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #38bdf8;
            font-size: 1.4rem;
            box-shadow: 0 0 25px rgba(56, 189, 248, 0.2);
        }
        .viento-titulo {
            font-family: 'Cinzel', serif;
            font-size: 2.5rem;
            letter-spacing: 2px;
            color: #f8fafc;
            margin-bottom: 0.5rem;
        }
        .viento-subtitulo {
            font-family: 'Lora', serif;
            font-style: italic;
            color: #94a3b8;
            line-height: 1.6;
            font-size: 1rem;
        }

        .viento-alerta {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #6ee7b7;
            padding: 12px 18px;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        /* Panel Admin */
        .admin-viento-panel {
            background: rgba(30, 41, 59, 0.65);
            border: 1px solid rgba(245, 158, 11, 0.4);
            border-radius: 12px;
            margin-bottom: 2rem;
            overflow: hidden;
        }
        .admin-viento-header {
            padding: 12px 18px;
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }
        .admin-viento-header h3 { font-size: 0.95rem; margin: 0; }
        .admin-viento-form { padding: 1.25rem; }
        .admin-viento-form.oculto { display: none; }
        .campo-grupo { display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px; flex: 1; }
        .campo-grupo label { font-size: 0.8rem; color: #cbd5e1; }
        .campo-fila { display: flex; gap: 12px; flex-wrap: wrap; }
        .admin-viento-form input, .admin-viento-form textarea {
            background: #090d16;
            border: 1px solid #334155;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
        }
        .btn-guardar-acertijo {
            background: #d97706;
            color: #fff;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        /* Compositor */
        .compositor-card {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(56, 189, 248, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            max-width: 720px;
            margin: 0 auto 2.5rem;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }
        .compositor-top {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }
        .input-apodo-wrap {
            flex: 1;
            min-width: 220px;
            display: flex;
            align-items: center;
            background: rgba(9, 13, 22, 0.8);
            border: 1px solid #1e293b;
            border-radius: 10px;
            padding: 0 12px;
            color: #64748b;
        }
        .input-apodo-wrap input {
            background: transparent;
            border: none;
            color: #f1f5f9;
            padding: 10px 8px;
            width: 100%;
            outline: none;
        }
        .select-tipo-susurro {
            background: rgba(9, 13, 22, 0.8);
            border: 1px solid #1e293b;
            color: #e2e8f0;
            padding: 10px 14px;
            border-radius: 10px;
            outline: none;
        }
        .compositor-card textarea {
            width: 100%;
            background: rgba(9, 13, 22, 0.8);
            border: 1px solid #1e293b;
            border-radius: 10px;
            padding: 12px;
            color: #f8fafc;
            font-family: 'Lora', serif;
            font-size: 1rem;
            resize: vertical;
            outline: none;
            box-sizing: border-box;
        }
        .compositor-card textarea:focus { border-color: #38bdf8; }
        .compositor-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
        }
        .contador-letras { font-size: 0.8rem; color: #64748b; }
        .btn-soplar {
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: #fff;
            border: none;
            padding: 10px 22px;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-soplar:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.4);
        }

        /* Filtros */
        .viento-filtros {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }
        .filtro-btn {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(255,255,255,0.08);
            color: #94a3b8;
            padding: 7px 16px;
            border-radius: 999px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        .filtro-btn.activo, .filtro-btn:hover {
            background: rgba(56, 189, 248, 0.15);
            border-color: #38bdf8;
            color: #f8fafc;
        }

        /* Muro de Tarjetas */
        .muro-susurros {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .tarjeta-susurro {
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: 14px;
            padding: 1.4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }
        .tarjeta-susurro:hover {
            transform: translateY(-4px);
            border-color: rgba(56, 189, 248, 0.35);
        }
        .susurro-cabecera {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .susurro-autor {
            font-size: 0.85rem;
            color: #cbd5e1;
            font-weight: 600;
        }
        .susurro-badge {
            font-size: 0.72rem;
            padding: 3px 10px;
            border-radius: 999px;
            background: rgba(56, 189, 248, 0.12);
            color: #38bdf8;
        }
        .susurro-texto {
            font-family: 'Lora', serif;
            font-style: italic;
            font-size: 1.02rem;
            line-height: 1.65;
            color: #f1f5f9;
            margin: 0 0 1.25rem;
            word-break: break-word;
        }
        .susurro-pie {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid rgba(255,255,255,0.06);
            padding-top: 0.75rem;
            font-size: 0.78rem;
            color: #64748b;
        }
        .btn-dar-aliento {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.1);
            color: #94a3b8;
            padding: 4px 12px;
            border-radius: 999px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-dar-aliento:hover, .btn-dar-aliento.soplado {
            background: rgba(56, 189, 248, 0.2);
            border-color: #38bdf8;
            color: #38bdf8;
        }
        .btn-borrar-susurro {
            background: transparent;
            border: none;
            color: #ef4444;
            cursor: pointer;
            margin-left: 6px;
        }
        .viento-vacio {
            grid-column: 1 / -1;
            text-align: center;
            padding: 3rem;
            color: #64748b;
        }
        .viento-vacio i { font-size: 2.5rem; margin-bottom: 0.5rem; }
    </style>

    <script>
        // Filtrar tarjetas por categoría sin recargar la página
        function filtrarSusurros(categoria, boton) {
            document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('activo'));
            boton.classList.add('activo');

            document.querySelectorAll('.tarjeta-susurro').forEach(card => {
                if (categoria === 'Todos' || card.dataset.categoria === categoria) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Dar "Aliento" (Like) vía AJAX
        async function darAliento(id, btn) {
            try {
                const res = await fetch(`/soplar-al-viento/${id}/soplar`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    const data = await res.json();
                    btn.querySelector('.conteo-soplos').textContent = data.soplos;
                    btn.classList.add('soplado');
                }
            } catch (e) {
                console.error(e);
            }
        }
    </script>
</x-app-layout>