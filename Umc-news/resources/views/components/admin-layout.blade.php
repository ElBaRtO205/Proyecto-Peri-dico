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

    <!-- Aplica el tema guardado antes de renderizar (evita parpadeo) -->
    <script>
        (function () {
            const guardado = localStorage.getItem('umc_theme');
            if (guardado) document.documentElement.setAttribute('data-theme', guardado);
        })();
    </script>
</head>

<body>
    <!-- Barra superior con la fecha del día -->
    <div class="barra-superior">
        <time class="barra-superior__fecha" datetime="{{ now()->format('Y-m-d') }}">
            <i class="fas fa-calendar-days"></i>
            {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
        </time>
    </div>

    <!-- Header con el logo -->
    <header class="encabezado">
        <div class="encabezado__contenido">
            <h1 class="encabezado__titulo">
                <span class="encabezado__titulo-acento">UMC</span> News
            </h1>
            <p class="encabezado__subtitulo">Periódico Oficial de la Universidad Marítima del Caribe</p>
        </div>
    </header>

    <div class="container">
        {{ $slot }}
    </div>
</body>
</html>
