<?php

use App\Models\Categoria;
use App\Models\Noticia;
use App\Models\NoticiaAuditoria;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function crearCategoriaRastro(): Categoria
{
    return Categoria::create([
        'nombre' => 'Rastro '.uniqid(),
        'descripcion' => 'Categoría de pruebas',
    ]);
}

function crearAdminRastro(): Usuario
{
    return Usuario::create([
        'nombre_completo' => 'Admin de rastro',
        'usuario' => 'admin-rastro-'.uniqid(),
        'email' => 'admin-rastro-'.uniqid().'@example.test',
        'contrasena' => Hash::make('password-seguro'),
        'rol' => 'Administrador',
    ]);
}

function crearNoticiaRastro(Categoria $categoria, array $extra = []): Noticia
{
    return Noticia::create(array_merge([
        'id_categoria' => $categoria->id_categoria,
        'titulo' => 'Noticia de rastro '.uniqid(),
        'contenido' => 'Contenido de prueba con suficiente texto.',
        'autor' => 'Redacción UMC',
        'status' => 'publicado',
    ], $extra));
}

test('the application works in the Caracas timezone', function () {
    expect(config('app.timezone'))->toBe('America/Caracas')
        ->and(now()->timezoneName)->toBe('America/Caracas');
});

test('the most recent published news is promoted when the main news becomes a draft', function () {
    $admin = crearAdminRastro();
    $categoria = crearCategoriaRastro();

    $principal = crearNoticiaRastro($categoria, [
        'titulo' => 'Principal actual',
        'es_principal' => true,
    ]);
    $candidata = crearNoticiaRastro($categoria, [
        'titulo' => 'Candidata a principal',
        'es_principal' => false,
    ]);

    $this->actingAs($admin)
        ->put(route('admin.noticias.update', $principal->id_noticia), [
            'id_categoria' => $categoria->id_categoria,
            'titulo' => 'Principal actual',
            'contenido' => $principal->contenido,
            'autor' => $principal->autor,
            'status' => 'borrador',
            'es_principal' => false,
        ])
        ->assertRedirect(route('admin.noticias.index'));

    expect($principal->refresh()->es_principal)->toBeFalse()
        ->and($candidata->refresh()->es_principal)->toBeTrue();
});

test('another main news is promoted when the main news is deleted', function () {
    $admin = crearAdminRastro();
    $categoria = crearCategoriaRastro();

    $principal = crearNoticiaRastro($categoria, [
        'titulo' => 'Principal a eliminar',
        'es_principal' => true,
    ]);
    $candidata = crearNoticiaRastro($categoria, [
        'titulo' => 'Principal de reemplazo',
        'es_principal' => false,
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.noticias.destroy', $principal->id_noticia))
        ->assertRedirect(route('admin.noticias.index'));

    expect(Noticia::find($principal->id_noticia))->toBeNull()
        ->and($candidata->refresh()->es_principal)->toBeTrue();
});

test('scheduled news stays hidden until its publishing time arrives', function () {
    $categoria = crearCategoriaRastro();

    $programada = crearNoticiaRastro($categoria, [
        'titulo' => 'Noticia programada para el futuro',
        'status' => 'programado',
        'programada_para' => now()->addDay(),
    ]);

    expect($programada->fecha_publicacion->toDateString())
        ->toBe(now()->addDay()->toDateString())
        ->and(Noticia::publicados()->where('id_noticia', $programada->id_noticia)->exists())
        ->toBeFalse();

    $this->get(route('noticia.show', $programada->slug))->assertNotFound();

    $programada->programada_para = now()->subMinute();
    $programada->save();

    expect(Noticia::publicados()->where('id_noticia', $programada->id_noticia)->exists())
        ->toBeTrue();

    $this->get(route('noticia.show', $programada->slug))->assertOk();
});

test('the scheduled publishing command converts due news to published', function () {
    $categoria = crearCategoriaRastro();

    $vencida = crearNoticiaRastro($categoria, [
        'titulo' => 'Programada ya vencida',
        'status' => 'programado',
        'programada_para' => now()->subMinutes(5),
    ]);
    $futura = crearNoticiaRastro($categoria, [
        'titulo' => 'Programada futura',
        'status' => 'programado',
        'programada_para' => now()->addHour(),
    ]);

    $this->artisan('noticias:publicar-programadas')->assertSuccessful();

    expect($vencida->refresh()->status)->toBe('publicado')
        ->and($futura->refresh()->status)->toBe('programado');
});

test('news keeps timestamps and every change is audited', function () {
    $admin = crearAdminRastro();
    $categoria = crearCategoriaRastro();

    $this->actingAs($admin);

    $noticia = crearNoticiaRastro($categoria, [
        'titulo' => 'Noticia auditada',
    ]);

    expect($noticia->created_at)->not->toBeNull()
        ->and($noticia->updated_at)->not->toBeNull();

    $creado = NoticiaAuditoria::where('accion', 'creado')
        ->where('titulo', 'Noticia auditada')
        ->first();

    expect($creado)->not->toBeNull()
        ->and($creado->id_usuario)->toBe($admin->id_usuario);

    $this->travel(2)->minutes();

    $noticia->titulo = 'Noticia auditada y editada';
    $noticia->save();

    $actualizado = NoticiaAuditoria::where('accion', 'actualizado')
        ->where('titulo', 'Noticia auditada y editada')
        ->first();

    expect($actualizado)->not->toBeNull()
        ->and($actualizado->cambios['despues'])->toHaveKey('titulo')
        ->and($actualizado->cambios['antes']['titulo'])->toBe('Noticia auditada');

    $noticia->delete();

    $eliminado = NoticiaAuditoria::where('accion', 'eliminado')->first();

    expect($eliminado)->not->toBeNull()
        ->and($eliminado->titulo)->toBe('Noticia auditada y editada');
});

test('administrators can review the change history', function () {
    $admin = crearAdminRastro();
    $categoria = crearCategoriaRastro();

    $this->actingAs($admin);

    $noticia = crearNoticiaRastro($categoria, [
        'titulo' => 'Noticia visible en auditoría',
    ]);

    $this->get(route('admin.noticias.auditoria'))
        ->assertOk()
        ->assertSee('Historial de cambios')
        ->assertSee('Noticia visible en auditoría')
        ->assertSee($admin->nombre_completo);
});
