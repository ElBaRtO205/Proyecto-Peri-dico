<?php 

namespace App\Services\Post;
use App\Models\post as Post;
use \Illuminate\Contracts\Pagination\LengthAwarePaginator;


class PostService
{

public function getall(): LengthAwarePaginator
{

$query = Post::query()->latest();

return $query->paginate(Post::PAGINATE);

}

public function find(int $id): Post
{
    return Post::findOrFail($id);
}
public function create(array $data): Post
    {
        $data['autor_id'] ??= 'anonymous';

        return Post::create($data);
    }
public function update(int $id, array $data): bool
    {
        $post = Post::findOrFail($id);
        $post->fill($data);

        return $post->save();
    }

public function delete(int $id): bool
    {
        $post = Post::findOrFail($id);

        return $post->delete();
    }


}