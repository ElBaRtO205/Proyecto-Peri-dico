<x-app-layout>
    <x-slot:title>UMC News | Página no encontrada (404)</x-slot:title>

    <section class="error-404">
        <div class="error-404__contenedor">
            <div class="error-404__ilustracion">
                <span class="error-404__codigo">404</span>
            </div>

            <h1 class="error-404__titulo">
                <i class="fas fa-newspaper-o error-404__icono"></i> ¡Ups! Noticia o página fuera de ruta
            </h1>

            <p class="error-404__descripcion">
                La noticia o sección que estás buscando no existe, ha sido movida o la dirección escrita no es correcta. 
                No te preocupes, la redacción sigue activa o no.
            </p>

            <div class="error-404__acciones">
                <a href="{{ route('home') }}" class="btn-error btn-error--primario">
                    <i class="fas fa-house"></i> Volver al Inicio
                </a>
                <button onclick="history.back()" class="btn-error btn-error--secundario">
                    <i class="fas fa-arrow-left"></i> Regresar a la página anterior
                </button>
            </div>
        </div>
    </section>
</x-app-layout>