<x-layout>
    <div class="row m-4">
        <div class="col-12">
            @if(session('message'))
                <div class="alert alert-secondary my-2">{{ session('message') }}</div>
            @endif

            <a href="{{ route('noticia.create') }}" class="btn btn-primary">Nueva Noticia</a>
        </div>

        <div class="col-12 mt-4">
            <ul>
                @foreach($noticias as $noticia)
                    <li class="mb-2">
                        <strong>{{ $noticia->titulo }}</strong> {{ $noticia->contenido }} ({{ $noticia->status }})
                        <a href="{{ route('noticia.edit', $noticia) }}" class="btn btn-warning">Editar</a>
                        <form method="POST" action="{{ route('noticia.destroy', $noticia) }}" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger">Eliminar</button>
                        </form>
                    </li>
                @endforeach
            </ul>

            {{ $noticias->links() }}
        </div>
    </div>
</x-layout>
