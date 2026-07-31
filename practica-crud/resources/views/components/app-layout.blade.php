<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="UMC News — Periódico oficial de la Universidad Marítima del Caribe." />
    <title>{{ $title ?? 'UMC News' }}</title>
    
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- SCRIPT ANTI-PARPADEO (Carga temas y fuentes guardadas antes del render) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('umc_theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            }
            const savedScale = localStorage.getItem('umc_font_scale');
            if (savedScale) {
                document.documentElement.style.setProperty('--tamano-fuente-base', savedScale + '%');
            }
        })();
    </script>
</head>

<body>

    <div class="barra-superior">
        <time class="barra-superior__fecha" datetime="{{ now()->format('Y-m-d') }}">
            <i class="fas fa-calendar-days"></i> {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
        </time>

        <div class="barra-superior__acciones">
            <!-- COMPONENTE DE ACCESIBILIDAD (A+/A- y Modo Oscuro) -->
            <x-toolbar/>

            <ul class="barra-superior__lista">
                <li class="barra-superior__item"><a class="barra-superior__enlace" href="#"><i class="fas fa-envelope"></i> Contacto</a></li>
                <li class="barra-superior__item"><a class="barra-superior__enlace" href="#"><i class="fas fa-microphone-lines"></i> Podcast</a></li>
                <li class="barra-superior__item">
                    <a href="{{ route('login') }}" class="encabezado__enlace">
                        <i class="fas fa-right-to-bracket"></i> Iniciar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <header class="encabezado">
        <div class="encabezado__contenido">
            <h1 class="encabezado__titulo"><span class="encabezado__titulo-acento">UMC</span> News</h1>
            <p class="encabezado__subtitulo">Periódico Oficial de la Universidad Marítima del Caribe</p>
        </div>
        <nav class="encabezado__nav" aria-label="Navegación principal">
            <ul class="encabezado__lista">
                @foreach($categorias as $categoria)
                    <li class="encabezado__item">
                        <a class="encabezado__enlace" href="{{ route('noticia.categoria', $categoria->id_categoria) }}">
                            {{ $categoria->nombre }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </header>

    <main class="contenido-principal" id="contenido-principal">
        {{ $slot }}
    </main>

    <!-- Footer con información del periódico -->
    <footer class="pie-pagina">
        <div class="pie-pagina__superior">
            <div class="pie-pagina__marca">
                <p class="pie-pagina__nombre">UMC News</p>
                <p class="pie-pagina__descripcion">Periódico oficial de la Universidad Marítima del Caribe</p>
            </div>

            <nav class="pie-pagina__nav" aria-label="Secciones del periódico">
                <h4 class="pie-pagina__titulo">Secciones</h4>
                <ul class="pie-pagina__lista">
                    <li class="pie-pagina__item"><a class="pie-pagina__enlace" href="#">Cultura</a></li>
                    <li class="pie-pagina__item"><a class="pie-pagina__enlace" href="#">Deportes</a></li>
                    <li class="pie-pagina__item"><a class="pie-pagina__enlace" href="#">Académico &amp; Proyectos</a></li>
                    <li class="pie-pagina__item"><a class="pie-pagina__enlace" href="#">Rumores del Campus</a></li>
                </ul>
            </nav>

            <nav class="pie-pagina__nav" aria-label="Información de la redacción">
                <h4 class="pie-pagina__titulo">La Redacción</h4>
                <ul class="pie-pagina__lista">
                    <li class="pie-pagina__item"><a class="pie-pagina__enlace" href="#">Quiénes somos</a></li>
                    <li class="pie-pagina__item"><a class="pie-pagina__enlace" href="#">Política editorial</a></li>
                    <li class="pie-pagina__item"><a class="pie-pagina__enlace" href="#">Equipo {{ date('Y') }}</a></li>
                </ul>
            </nav>

            <nav class="pie-pagina__nav" aria-label="Cómo participar">
                <h4 class="pie-pagina__titulo">Participa</h4>
                <ul class="pie-pagina__lista">
                    <li class="pie-pagina__item"><a class="pie-pagina__enlace" href="#">Enviar una nota</a></li>
                    <li class="pie-pagina__item"><a class="pie-pagina__enlace" href="#">Carta al editor</a></li>
                    <li class="pie-pagina__item"><a class="pie-pagina__enlace" href="#">Voluntariado</a></li>
                </ul>
            </nav>
        </div>

        <div class="pie-pagina__inferior">
            <p>© <time datetime="{{ date('Y') }}">{{ date('Y') }}</time> UMC News — Universidad Marítima del Caribe. Todos los derechos reservados.</p>
        </div>
    </footer>

   
    <script src="{{ asset('js/toolbar.js') }}" defer></script>
    <script src="{{ asset('js/home.js') }}" defer></script> 
</body>
</html>