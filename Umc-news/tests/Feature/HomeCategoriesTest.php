<?php

use App\Models\Categoria;
use App\Models\Noticia;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function crearCategoriaHome(string $nombre): Categoria
{
    return Categoria::create([
        'nombre' => $nombre,
        'descripcion' => 'Sección de prueba',
    ]);
}

function crearNoticiaHome(Categoria $categoria, string $titulo, array $extra = []): Noticia
{
    return Noticia::create(array_merge([
        'id_categoria' => $categoria->id_categoria,
        'titulo' => $titulo,
        'contenido' => 'Contenido de prueba para la portada dinámica.',
        'autor' => 'Redacción UMC',
        'status' => 'publicado',
    ], $extra));
}

test('the home page generates sections from the real categories', function () {
    $tecnologia = crearCategoriaHome('Tecnología');
    $gastronomia = crearCategoriaHome('Gastronomía');

    crearNoticiaHome($tecnologia, 'Nueva carrera de tecnología');
    crearNoticiaHome($gastronomia, 'Festival gastronómico universitario');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Tecnología')
        ->assertSee('Nueva carrera de tecnología')
        ->assertSee('Gastronomía')
        ->assertSee('Festival gastronómico universitario')
        // Las secciones fijas que antes estaban hardcodeadas ya no se inventan.
        ->assertDontSee('Pronto nuevas noticias de Deportes.')
        ->assertDontSee('Pronto nuevas noticias de Economia.');
});

test('categories use their slug in public urls', function () {
    $categoria = crearCategoriaHome('Ciencias del Mar');
    crearNoticiaHome($categoria, 'Noticia de ciencias marinas');

    expect($categoria->slug)->toBe('ciencias-del-mar');

    $url = route('noticia.categoria', $categoria);

    expect($url)->toContain('/categoria/ciencias-del-mar')
        ->and($url)->not->toContain('/categoria/'.$categoria->id_categoria);

    $this->get($url)
        ->assertOk()
        ->assertSee('Ciencias del Mar')
        ->assertSee('Noticia de ciencias marinas');
});

test('public pages expose their own entrance transition', function () {
    $categoria = crearCategoriaHome('Campus');
    $noticia = crearNoticiaHome($categoria, 'Noticia con transición');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('pagina--home', false);

    $this->get(route('noticia.categoria', $categoria))
        ->assertOk()
        ->assertSee('pagina--categoria', false);

    $this->get(route('noticia.show', $noticia))
        ->assertOk()
        ->assertSee('pagina--noticia', false);
});

test('old numeric category urls redirect to the slug url', function () {
    $categoria = crearCategoriaHome('Innovación');

    $this->get('/categoria/'.$categoria->id_categoria)
        ->assertRedirect(route('noticia.categoria', $categoria));
});

test('the see more link always points to the category page', function () {
    $categoria = crearCategoriaHome('Tecnología');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('href="'.route('noticia.categoria', $categoria).'"', false)
        ->assertDontSee('href="#" class="grilla-noticias__enlace-mas"', false);
});

test('the article shows its estimated reading time', function () {
    $categoria = crearCategoriaHome('Cultura');
    $noticia = crearNoticiaHome($categoria, 'Noticia con tiempo de lectura', [
        'contenido' => trim(str_repeat('palabra ', 450)),
    ]);

    expect($noticia->tiempo_lectura)->toBe(3);

    $this->get(route('noticia.show', $noticia))
        ->assertOk()
        ->assertSee('3 min de lectura');
});

test('related news are filtered by the current category', function () {
    $categoria = crearCategoriaHome('Cultura');
    $otraCategoria = crearCategoriaHome('Deportes');

    $principal = crearNoticiaHome($categoria, 'Noticia principal de cultura');
    $relacionada = crearNoticiaHome($categoria, 'Otra noticia de cultura');
    $ajena = crearNoticiaHome($otraCategoria, 'Noticia de deportes que no debe aparecer');

    $this->get(route('noticia.show', $principal))
        ->assertOk()
        ->assertSee('Otra noticia de cultura')
        ->assertDontSee('Noticia de deportes que no debe aparecer')
        ->assertDontSee($ajena->titulo);
});
