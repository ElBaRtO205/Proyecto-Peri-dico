<?php

use App\Models\Categoria;
use App\Models\Noticia;
use App\Models\Usuario;
use App\Services\Post\PostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('the edit form loads and posts to the numeric id url', function () {
    $categoria = Categoria::create([
        'nombre' => 'Noticias',
        'descripcion' => 'Categoría de pruebas',
    ]);
    $admin = Usuario::create([
        'nombre_completo' => 'Admin de prueba',
        'usuario' => 'admin-edit',
        'email' => 'admin-edit@example.test',
        'contrasena' => Hash::make('password-seguro'),
        'rol' => 'Administrador',
    ]);
    $noticia = Noticia::create([
        'id_categoria' => $categoria->id_categoria,
        'titulo' => 'Noticia a editar',
        'contenido' => 'Contenido original',
        'autor' => 'Redacción',
        'status' => 'publicado',
    ]);

    // El formulario de edición debe cargar (antes reventaba con TypeError
    // porque la URL salía con el slug y el controlador espera int).
    $response = $this->actingAs($admin)
        ->get(route('admin.noticias.edit', $noticia->id_noticia))
        ->assertOk();

    // El action del formulario debe apuntar al ID numérico, no al slug
    $response->assertSee(
        'action="'.route('admin.noticias.update', $noticia->id_noticia).'"',
        false
    );
});

test('updating the featured article switches the featured flag', function () {
    $categoria = Categoria::create([
        'nombre' => 'Noticias',
        'descripcion' => 'Categoría de pruebas',
    ]);
    $admin = Usuario::create([
        'nombre_completo' => 'Admin de prueba',
        'usuario' => 'admin-noticias',
        'email' => 'admin-noticias@example.test',
        'contrasena' => Hash::make('password-seguro'),
        'rol' => 'Administrador',
    ]);
    $noticiaPrincipalAnterior = Noticia::create([
        'id_categoria' => $categoria->id_categoria,
        'titulo' => 'Noticia principal anterior',
        'contenido' => 'Contenido anterior',
        'autor' => 'Redacción',
        'es_principal' => true,
        'status' => 'publicado',
    ]);
    $noticiaNuevaPrincipal = Noticia::create([
        'id_categoria' => $categoria->id_categoria,
        'titulo' => 'Noticia que será principal',
        'contenido' => 'Contenido actualizado',
        'autor' => 'Redacción',
        'es_principal' => false,
        'status' => 'publicado',
    ]);

    $this->actingAs($admin)
        ->put(route('admin.noticias.update', $noticiaNuevaPrincipal->id_noticia), [
            'id_categoria' => $categoria->id_categoria,
            'titulo' => 'Noticia actualizada',
            'contenido' => 'Contenido actualizado',
            'autor' => 'Redacción',
            'status' => 'publicado',
            'es_principal' => true,
        ])
        ->assertRedirect(route('admin.noticias.index'));

    expect($noticiaPrincipalAnterior->refresh()->es_principal)->toBeFalse()
        ->and($noticiaNuevaPrincipal->refresh()->es_principal)->toBeTrue();
});

test('failed article update rolls back the featured article reset', function () {
    $categoria = Categoria::create([
        'nombre' => 'Noticias',
        'descripcion' => 'Categoría de pruebas',
    ]);
    $admin = Usuario::create([
        'nombre_completo' => 'Admin de prueba',
        'usuario' => 'admin-rollback',
        'email' => 'admin-rollback@example.test',
        'contrasena' => Hash::make('password-seguro'),
        'rol' => 'Administrador',
    ]);
    $noticiaPrincipal = Noticia::create([
        'id_categoria' => $categoria->id_categoria,
        'titulo' => 'Noticia principal',
        'contenido' => 'Contenido original',
        'autor' => 'Redacción',
        'es_principal' => true,
        'status' => 'publicado',
    ]);
    $noticiaAEditar = Noticia::create([
        'id_categoria' => $categoria->id_categoria,
        'titulo' => 'Otra noticia',
        'contenido' => 'Contenido original',
        'autor' => 'Redacción',
        'es_principal' => false,
        'status' => 'publicado',
    ]);

    $postService = Mockery::mock(PostService::class);
    $postService->shouldReceive('find')
        ->once()
        ->with($noticiaAEditar->id_noticia)
        ->andReturn($noticiaAEditar);
    $postService->shouldReceive('update')
        ->once()
        ->with($noticiaAEditar->id_noticia, Mockery::type('array'))
        ->andReturn(false);
    $this->app->instance(PostService::class, $postService);
    $this->withoutExceptionHandling();

    expect(fn () => $this->actingAs($admin)->put(
        route('admin.noticias.update', $noticiaAEditar->id_noticia),
        [
            'id_categoria' => $categoria->id_categoria,
            'titulo' => 'Título nuevo',
            'contenido' => 'Contenido nuevo',
            'autor' => 'Redacción',
            'status' => 'publicado',
            'es_principal' => true,
        ]
    ))->toThrow(RuntimeException::class);

    expect($noticiaPrincipal->refresh()->es_principal)->toBeTrue();
});
