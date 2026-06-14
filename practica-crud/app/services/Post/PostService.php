<?php 

namespace App\Services\Post;
use App\Models\noticia as Noticia;
use \Illuminate\Contracts\Pagination\LengthAwarePaginator;


class PostService
{

public function getall(): LengthAwarePaginator
{

$query = Noticia::query()->latest('fecha_publicacion');

return $query->paginate(Noticia::PAGINATE);

}

public function find(int $id): Noticia
{
    return Noticia::findOrFail($id);
}
public function create(array $data): Noticia
    {
        //$data['id_autor'] ??= 'anonymous';

       //dd($data);
        return Noticia::create($data);
    }
public function update(int $id, array $data): bool
    {
        $post = Noticia::findOrFail($id);
        $post->fill($data);

        return $post->save();
    }

public function delete(int $id): bool
    {
        $post = Noticia::findOrFail($id);

        return $post->delete();
    }


}