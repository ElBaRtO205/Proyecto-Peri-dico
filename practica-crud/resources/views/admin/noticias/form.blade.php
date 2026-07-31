<x-admin-layout>
    <div class="admin-form-container">
        <div class="admin-card">
            
            <!-- Cabecera -->
            <div class="admin-card-header">
                <h2>{{ $noticia->exists ? 'Editar Noticia' : 'Crear Nueva Noticia' }}</h2>
                <a href="{{ route('admin.noticias.index') }}" class="btn-cancel">&larr; Volver</a>
            </div>

            <!-- Errores -->
            @if ($errors->any())
                <div class="alert-error-box">
                    <strong>Por favor corrige los errores:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $noticia->exists ? route('admin.noticias.update', $noticia) : route('admin.noticias.store') }}" enctype="multipart/form-data">
                @csrf
                @if($noticia->exists) @method('PUT') @endif

                <!-- Título -->
                <div class="form-group">
                    <label for="titulo">Título de la noticia</label>
                    <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo', $noticia->titulo) }}" placeholder="Ej: Avances en los laboratorios..." required>
                </div>

                <!-- Contenido -->
                <div class="form-group">
                    <label for="contenido">Contenido</label>
                    <textarea name="contenido" id="contenido" class="form-control" placeholder="Escribe la redacción aquí..." required>{{ old('contenido', $noticia->contenido) }}</textarea>
                </div>

                <!-- DestacarNoticia -->
                <div class="checkbox-group">
                    <label class="checkbox-label" for="es_principal">
                        <input type="checkbox" name="es_principal" id="es_principal" value="1" {{ old('es_principal', $noticia->es_principal) ? 'checked' : '' }}>
                        <div>
                            <strong>¿Destacar como noticia principal?</strong>
                            <p class="help-text">Si marcas esta opción, se mostrará de primero en la portada y se quitará el destacado a la noticia anterior.</p>
                        </div>
                    </label>
                </div>

                <!-- Fila de Status y Categoría -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="borrador" @selected(old('status', $noticia->status) === 'borrador')>Borrador</option>
                            <option value="publicado" @selected(old('status', $noticia->status) === 'publicado')>Publicado</option>
                        </select>
                    </div>

                    <div class="form-group">    
                        <label for="id_categoria">Categoría</label>
                        <select name="id_categoria" id="id_categoria" class="form-control" required>
                            <option value="">Selecciona una categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id_categoria }}" {{ (old('id_categoria', $noticia->id_categoria) == $categoria->id_categoria) ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Autor -->
                <div class="form-group">
                    <label for="autor">Nombre del Autor</label>
                    <input type="text" name="autor" id="autor" class="form-control" value="{{ old('autor', $noticia->autor) }}" placeholder="Ej: Juan Pérez" required>
                </div>

                <!-- Imagen -->
                <div class="form-group">
                    <label for="imagen_noticia">Imagen de la noticia</label>
                    @if($noticia->exists && $noticia->imagen_noticia)
                        <div class="img-preview">
                            <small>Imagen actual:</small>
                            <img src="{{ asset('storage/' . $noticia->imagen_noticia) }}" alt="Vista previa">
                        </div>
                    @endif
                    <input type="file" name="imagen_noticia" id="imagen_noticia" class="form-control" accept="image/*">
                </div>

                <!-- Botones -->
                <div class="form-actions">
                    <a href="{{ route('admin.noticias.index') }}" class="btn-cancel">Cancelar</a>
                    <button type="submit" class="btn-submit">{{ $noticia->exists ? 'Actualizar Noticia' : 'Crear Noticia' }}</button>
                </div>

            </form>
        </div>
    </div>
</x-admin-layout>