<x-layout>
    <div class="row justify-content-center">
        <div class="col-md-4">
            <form method="POST" action="{{ $noticia->exists ? route('noticia.update', $noticia) : route('noticia.store') }}" enctype="multipart/form-data">
                @csrf
                @if($noticia->exists) @method('PUT') @endif

                <div class="form-group">
                    <label>Título</label>
                    <input name="titulo" class="form-control" value="{{ old('titulo', $noticia->titulo) }}">
                    @error('titulo') <div>{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Contenido</label>
                    <textarea name="contenido" class="form-control">{{ old('contenido', $noticia->contenido) }}</textarea>
                    @error('contenido') <div>{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="borrador" @selected(old('status', $noticia->status) === 'borrador')>Borrador</option>
                        <option value="publicado" @selected(old('status', $noticia->status) === 'publicado')>Publicado</option>
                    </select>
                    @error('status') <div>{{ $message }}</div> @enderror
                </div>

                <div class="form-group">    
                <label>Categoría:</label>
                <select name="id_categoria" required>
                    <option value="">Selecciona una categoría</option>
                    

                    @foreach($categorias as $categoria)
                       <!-- <option value="{{ $categoria->id_categoria }}">{{ $categoria->nombre }}</option> -->

                     <option value="{{ $categoria->id_categoria }}" 
            {{ (old('id_categoria', $noticia->id_categoria) == $categoria->id_categoria) ? 'selected' : '' }}>
            {{ $categoria->nombre }}
        </option>
                    @endforeach
                </select>
                <div class="form-group">
                  <label for="autor">Nombre del Autor:</label>
                    <input type="text" name="autor" id="autor" class="form-control" 
                    value="{{ old('autor', $noticia->autor) }}" placeholder="Ej: Juan Pérez" required>
               </div>
                          
                <div class="form-group mt-3">
                    <label for="imagen_noticia">Imagen de la noticia</label>
                    <input type="file" name="imagen_noticia" id="imagen_noticia" class="form-control" accept="image/*">
                    @error('imagen_noticia')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>


                <button class="btn btn-primary mt-2">{{ $noticia->exists ? 'Actualizar' : 'Crear' }}</button>
            </form>
            @if ($errors->any())
    <div class="alert alert-danger" style="background-color: #f8d7da; padding: 15px; margin-bottom: 20px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        </div>
    </div>
</x-layout>
