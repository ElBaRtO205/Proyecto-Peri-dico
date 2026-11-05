<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description"
        content="UMC News — Periódico oficial de la Universidad Marítima del Caribe. Noticias de deportes, cultura, académico y rumores del campus." />
    <title>UMC News | Gestión de contenidos</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Lora:ital,wght@0,400;0,500;1,400&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/estilos_crud_principal.css') }}">
</head>

<body class="admin-body">
    <a class="skip-link" href="#contenido-principal">Saltar al contenido principal</a>

    @php
        $adminNoLeidos = \App\Models\MensajeContacto::where('leido', false)->count();
        $adminPendientes = \App\Models\Comentario::pendientes()->count();
    @endphp

    <div class="admin-shell">
        <aside class="admin-sidebar" id="adminSidebar" aria-label="Navegación del panel">
            <div class="admin-sidebar__brand">
                <a href="{{ route('admin.noticias.index') }}" class="admin-sidebar__logo">
                    <span class="admin-sidebar__logo-mark">UMC</span>
                    <span class="admin-sidebar__logo-text">
                        News
                        <small>Administración</small>
                    </span>
                </a>

                <button type="button" class="admin-sidebar__close" data-admin-menu-close
                        aria-label="Cerrar menú de navegación">
                    <i class="fas fa-xmark" aria-hidden="true"></i>
                </button>
            </div>

            <nav class="admin-nav">
                <span class="admin-nav__label">Contenido</span>

                <a href="{{ route('admin.noticias.index') }}"
                   class="admin-nav__link {{ request()->routeIs('admin.noticias.index') ? 'is-active' : '' }}">
                    <i class="fas fa-gauge-high" aria-hidden="true"></i>
                    <span>Panel</span>
                </a>

                <a href="{{ route('admin.comentarios.index') }}"
                   class="admin-nav__link {{ request()->routeIs('admin.comentarios.*') ? 'is-active' : '' }}">
                    <i class="fas fa-comments" aria-hidden="true"></i>
                    <span>Comentarios</span>
                    @if($adminPendientes > 0)
                        <span class="admin-nav__badge">{{ $adminPendientes }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.mensajes.index') }}"
                   class="admin-nav__link {{ request()->routeIs('admin.mensajes.*') ? 'is-active' : '' }}">
                    <i class="fas fa-inbox" aria-hidden="true"></i>
                    <span>Mensajes</span>
                    @if($adminNoLeidos > 0)
                        <span class="admin-nav__badge admin-nav__badge--gold">{{ $adminNoLeidos }}</span>
                    @endif
                </a>

                <a href="{{ route('admin.noticias.auditoria') }}"
                   class="admin-nav__link {{ request()->routeIs('admin.noticias.auditoria') ? 'is-active' : '' }}">
                    <i class="fas fa-clock-rotate-left" aria-hidden="true"></i>
                    <span>Historial</span>
                </a>

                <span class="admin-nav__label">Comunidad</span>

                <a href="{{ route('secreto.index') }}" class="admin-nav__link">
                    <i class="fas fa-wind" aria-hidden="true"></i>
                    <span>Soplar al Viento</span>
                </a>
            </nav>

            <div class="admin-sidebar__footer">
                <div class="admin-user">
                    <span class="admin-user__avatar" aria-hidden="true">
                        {{ mb_strtoupper(mb_substr(auth()->user()?->nombre_completo ?? 'A', 0, 1)) }}
                    </span>
                    <span class="admin-user__info">
                        <strong>{{ auth()->user()?->nombre_completo ?? 'Administrador' }}</strong>
                        <small>{{ auth()->user()?->rol ?? 'Administrador' }}</small>
                    </span>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="admin-logout-form">
                    @csrf
                    <button type="submit" class="admin-logout">
                        <i class="fas fa-right-from-bracket" aria-hidden="true"></i>
                        <span>Cerrar sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="admin-overlay" data-admin-menu-close hidden></div>

        <div class="admin-main">
            <header class="admin-topbar">
                <button type="button" class="admin-topbar__toggle" data-admin-menu-toggle
                        aria-controls="adminSidebar" aria-expanded="false"
                        aria-label="Abrir menú de navegación">
                    <i class="fas fa-bars" aria-hidden="true"></i>
                </button>

                <time class="admin-topbar__date" datetime="{{ now()->format('Y-m-d') }}">
                    <i class="fas fa-calendar-days" aria-hidden="true"></i>
                    {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                </time>

                <a href="{{ route('home') }}" class="admin-topbar__link">
                    <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    Ver sitio
                </a>
            </header>

            <div class="admin-content" id="contenido-principal">
                {{ $slot }}
            </div>
        </div>
    </div>

    <script src="{{ asset('js/admin-panel.js') }}" defer></script>
</body>
</html>
