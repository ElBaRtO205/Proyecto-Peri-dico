<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="UMC News — Periódico oficial de la Universidad Marítima del Caribe. Noticias de deportes, cultura, académico y rumores del campus." />
    <title>Gestión de contenidos</title>
   <link rel="stylesheet" href="{{ asset('css/estilos_crud_principal.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link >
</head>

<body>
    <!-- Barra de arriba con la fecha y enlaces rápidos -->
    <div class="barra-superior">
        <time class="barra-superior__fecha" datetime="2026-05-24">
            <i class="fas fa-calendar-days"></i> Domingo, 24 de mayo de 2026
        </time>
    </div>

    <!-- Header con el logo, navegación y búsqueda -->
    <header class="encabezado">
        <div class="encabezado__contenido">
            <h1 class="encabezado__titulo">
                <span class="encabezado__titulo-acento">UMC</span> News
            </h1>
            <p class="encabezado__subtitulo">Periódico Oficial de la Universidad Marítima del Caribe</p>
        </div>
    </header>

<body>
    <div class="container">
        {{ $slot }}
    </div>

     <script src="{{ asset('js/admin.js') }}" defer></script>   
</body>
</html>