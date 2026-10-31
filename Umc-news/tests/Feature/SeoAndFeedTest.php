<?php

use App\Models\Categoria;
use App\Models\Noticia;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Crea una categoría y una noticia publicada de prueba.
 */
function crearNoticiaPrueba(array $extra = []): Noticia
{
    $categoria = Categoria::firstOrCreate(
        ['nombre' => 'Cultura'],
        ['descripcion' => 'Categoría de pruebas']
    );

    return Noticia::create(array_merge([
        'id_categoria' => $categoria->id_categoria,
        'titulo' => 'Noticia de prueba para el slug',
        'contenido' => 'Este es el contenido de la noticia used para las pruebas de la semana 2.',
        'autor' => 'Redacción UMC',
        'status' => 'publicado',
        'fecha_publicacion' => '2026-10-01',
    ], $extra));
}

test('a news item gets a slug generated from its title', function () {
    $noticia = crearNoticiaPrueba();

    expect($noticia->slug)->toBe('noticia-de-prueba-para-el-slug');
});

test('duplicate titles get unique slugs', function () {
    $primera = crearNoticiaPrueba();
    $segunda = crearNoticiaPrueba();

    expect($primera->slug)->toBe('noticia-de-prueba-para-el-slug')
        ->and($segunda->slug)->toBe('noticia-de-prueba-para-el-slug-1');
});

test('the public article url uses the slug', function () {
    $noticia = crearNoticiaPrueba();

    $this->get(route('noticia.show', $noticia->slug))
        ->assertOk()
        ->assertSee($noticia->titulo);
});

test('the article page includes seo meta and newsarticle json-ld', function () {
    $noticia = crearNoticiaPrueba();

    $this->get(route('noticia.show', $noticia->slug))
        ->assertOk()
        ->assertSee('property="og:title"', false)
        ->assertSee('property="og:image"', false)
        ->assertSee('name="twitter:card"', false)
        ->assertSee('rel="canonical"', false)
        ->assertSee('application/ld+json', false)
        ->assertSee('NewsArticle', false);
});

test('old numeric article urls redirect permanently to the slug url', function () {
    $noticia = crearNoticiaPrueba();

    $this->get('/noticia/' . $noticia->id_noticia)
        ->assertStatus(301)
        ->assertRedirect(route('noticia.show', $noticia->slug));
});

test('a draft news item is not reachable on the public url', function () {
    $noticia = crearNoticiaPrueba(['status' => 'borrador']);

    $this->get(route('noticia.show', $noticia->slug))->assertNotFound();
});

test('the rss feed returns valid xml with published news', function () {
    crearNoticiaPrueba();
    crearNoticiaPrueba(['titulo' => 'Segunda noticia del feed']);

    $response = $this->get('/feed.xml')->assertOk();

    expect($response->headers->get('Content-Type'))->toContain('application/xml');
    $response->assertSee('<rss', false)
        ->assertSee('<item>', false)
        ->assertSee('Segunda noticia del feed', false);
});

test('the sitemap lists the home, categories and published news', function () {
    $noticia = crearNoticiaPrueba();

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertSee('<urlset', false)
        ->assertSee(url('/'), false)
        ->assertSee($noticia->slug, false);
});

test('the home page advertises the rss feed for autodiscovery', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('type="application/rss+xml"', false);
});
