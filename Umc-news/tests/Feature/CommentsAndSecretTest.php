<?php

use App\Models\Acertijo;
use App\Models\Categoria;
use App\Models\Comentario;
use App\Models\Noticia;
use App\Models\Susurro;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function crearNoticiaComentarios(array $extra = []): Noticia
{
    $categoria = Categoria::create([
        'nombre' => 'Campus',
        'descripcion' => 'Categoría de pruebas',
    ]);

    return Noticia::create(array_merge([
        'id_categoria' => $categoria->id_categoria,
        'titulo' => 'Noticia con comentarios',
        'contenido' => 'Contenido de prueba para comentarios.',
        'autor' => 'Redacción UMC',
        'status' => 'publicado',
        'fecha_publicacion' => '2026-11-03',
    ], $extra));
}

function crearLectorComentarios(): Usuario
{
    return Usuario::create([
        'nombre_completo' => 'Lector de prueba',
        'usuario' => 'lector-'.uniqid(),
        'email' => 'lector-'.uniqid().'@example.test',
        'contrasena' => Hash::make('password-seguro'),
        'rol' => 'Lector',
    ]);
}

function crearAdminComentarios(): Usuario
{
    return Usuario::create([
        'nombre_completo' => 'Admin de prueba',
        'usuario' => 'admin-'.uniqid(),
        'email' => 'admin-'.uniqid().'@example.test',
        'contrasena' => Hash::make('password-seguro'),
        'rol' => 'Administrador',
    ]);
}

test('guests must sign in before commenting', function () {
    $noticia = crearNoticiaComentarios();

    $this->post(route('comentarios.store', $noticia->id_noticia), [
        'contenido' => 'Comentario anónimo',
    ])->assertRedirect(route('login'));

    expect(Comentario::count())->toBe(0);
});

test('a registered reader can send a comment that stays pending', function () {
    $noticia = crearNoticiaComentarios();
    $lector = crearLectorComentarios();

    $this->actingAs($lector)
        ->post(route('comentarios.store', $noticia->id_noticia), [
            'contenido' => 'Mi comentario sobre la noticia',
        ])
        ->assertRedirect()
        ->assertSessionHas('exito');

    $comentario = Comentario::firstOrFail();

    expect($comentario->status)->toBe(Comentario::STATUS_PENDIENTE)
        ->and($comentario->id_usuario)->toBe($lector->id_usuario)
        ->and($comentario->autor)->toBe($lector->nombre_completo);

    $this->get(route('noticia.show', $noticia))
        ->assertOk()
        ->assertDontSee('Mi comentario sobre la noticia');
});

test('comments cannot be sent to draft or missing news', function () {
    $borrador = crearNoticiaComentarios(['status' => 'borrador']);
    $lector = crearLectorComentarios();

    $this->actingAs($lector)
        ->post(route('comentarios.store', $borrador->id_noticia), [
            'contenido' => 'Comentario sobre borrador',
        ])
        ->assertNotFound();

    $this->actingAs($lector)
        ->post(route('comentarios.store', 999999), [
            'contenido' => 'Comentario sobre noticia inexistente',
        ])
        ->assertNotFound();

    expect(Comentario::count())->toBe(0);
});

test('the comment honeypot blocks suspicious submissions', function () {
    $noticia = crearNoticiaComentarios();
    $lector = crearLectorComentarios();

    $this->actingAs($lector)
        ->post(route('comentarios.store', $noticia->id_noticia), [
            'contenido' => 'Intento automatizado',
            'sitio_web' => 'https://spam.example',
        ])
        ->assertSessionHasErrors('sitio_web');

    expect(Comentario::count())->toBe(0);
});

test('only approved comments are visible on the article', function () {
    $noticia = crearNoticiaComentarios();
    $lector = crearLectorComentarios();

    Comentario::create([
        'id_noticia' => $noticia->id_noticia,
        'id_usuario' => $lector->id_usuario,
        'autor' => $lector->nombre_completo,
        'contenido' => 'Comentario aprobado visible',
        'status' => Comentario::STATUS_APROBADO,
    ]);

    Comentario::create([
        'id_noticia' => $noticia->id_noticia,
        'id_usuario' => $lector->id_usuario,
        'autor' => $lector->nombre_completo,
        'contenido' => 'Comentario pendiente invisible',
        'status' => Comentario::STATUS_PENDIENTE,
    ]);

    $this->get(route('noticia.show', $noticia))
        ->assertOk()
        ->assertSee('Comentario aprobado visible')
        ->assertDontSee('Comentario pendiente invisible');
});

test('administrators can approve a pending comment', function () {
    $noticia = crearNoticiaComentarios();
    $lector = crearLectorComentarios();
    $admin = crearAdminComentarios();

    $comentario = Comentario::create([
        'id_noticia' => $noticia->id_noticia,
        'id_usuario' => $lector->id_usuario,
        'autor' => $lector->nombre_completo,
        'contenido' => 'Comentario por aprobar',
        'status' => Comentario::STATUS_PENDIENTE,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.comentarios.aprobar', $comentario))
        ->assertRedirect();

    expect($comentario->refresh()->status)->toBe(Comentario::STATUS_APROBADO);

    $this->get(route('noticia.show', $noticia))
        ->assertSee('Comentario por aprobar');
});

test('guests are redirected to login when trying to enter the secret section', function () {
    $this->get(route('secreto.index'))->assertRedirect(route('login'));
});

test('registered readers need to solve the riddle before entering the secret section', function () {
    $lector = crearLectorComentarios();

    $this->actingAs($lector)
        ->get(route('secreto.index'))
        ->assertRedirect(route('home'));

    $acertijo = Acertijo::create([
        'pregunta' => '¿Cuál es la clave?',
        'respuesta' => 'viento',
        'activo' => true,
    ]);

    $this->actingAs($lector)
        ->withSession(['acertijo_resuelto_id' => $acertijo->id_acertijo])
        ->get(route('secreto.index'))
        ->assertOk();
});

test('a reader cannot publish a whisper without unlocking the section', function () {
    $lector = crearLectorComentarios();

    $this->actingAs($lector)
        ->post(route('secreto.publicar'), [
            'apodo' => 'Anónimo',
            'contenido' => 'Susurro sin desbloquear',
            'categoria_viento' => 'Confesión',
        ])
        ->assertForbidden();

    expect(Susurro::count())->toBe(0);
});
