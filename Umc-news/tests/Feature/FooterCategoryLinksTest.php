<?php

use App\Models\Categoria;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;

uses(RefreshDatabase::class);

test('footer section links point to their matching categories', function () {
    $cultura = Categoria::create(['nombre' => 'Cultura', 'descripcion' => 'Cultura']);
    $deportes = Categoria::create(['nombre' => 'Deportes', 'descripcion' => 'Deportes']);
    $campus = Categoria::create(['nombre' => 'Campus', 'descripcion' => 'Campus']);
    View::share('categorias', collect([$cultura, $deportes, $campus]));

    $response = $this->get(route('home'))->assertOk();

    $response->assertSee('href="'.route('noticia.categoria', $cultura->id_categoria).'"', false)
        ->assertSee('href="'.route('noticia.categoria', $deportes->id_categoria).'"', false)
        ->assertSee('href="'.route('noticia.categoria', $campus->id_categoria).'"', false)
        ->assertSee('Rumores del Campus')
        ->assertDontSee('href="#">Rumores del Campus', false);
});