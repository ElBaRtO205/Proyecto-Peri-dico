<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Post\CreatePostRequest;
use App\Services\Post\PostService;
use App\Models\Post;
use App\Http\Requests\Post\UpdatePostRequest;

class PostController extends Controller
{
    public function __construct(protected PostService $Service)
    {
    
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = $this->Service->getall();

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.form', ['post' => new Post()]);
    }

    /**
     * guarda el contenido
     */
    public function store(CreatePostRequest $request)
    {
           
    $this->Service->create($request->validated());

            return redirect()->route('posts.index')->with('message', 'se creo tu mamada de post bro');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $post = $this->Service->find($id);

        return view('posts.form', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, int $id)
    {
        $this ->Service->update($id, $request->validated());
        return redirect()->route('posts.index')->with('message', 'se actualizo tu mamada de post bro');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->Service->delete($id);
        return redirect()->route('posts.index')->with('message', 'se elimino tu mamada de post bro');
    }
}
