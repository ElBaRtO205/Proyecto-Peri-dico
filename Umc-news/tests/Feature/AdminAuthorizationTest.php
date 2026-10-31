<?php

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('guests are redirected to login when accessing news management', function () {
    $this->get(route('admin.noticias.index'))
        ->assertRedirect(route('login'));
});

test('editors cannot access news management', function () {
    $editor = new Usuario();
    $editor->forceFill(['rol' => 'Editor']);

    $this->actingAs($editor)
        ->get(route('admin.noticias.index'))
        ->assertForbidden();
});

test('editors can sign in but cannot access news management', function () {
    Usuario::create([
        'nombre_completo' => 'Editor de prueba',
        'usuario' => 'editor-prueba',
        'email' => 'editor@example.test',
        'contrasena' => Hash::make('password-seguro'),
        'rol' => 'Editor',
    ]);

    $this->post(route('login'), [
        'email' => 'editor@example.test',
        'password' => 'password-seguro',
    ])->assertRedirect(route('home'));

    $this->assertAuthenticatedAs(Usuario::where('email', 'editor@example.test')->first());

    $this->get(route('admin.noticias.index'))->assertForbidden();
});

test('registration creates and signs in an editor account', function () {
    $this->post(route('register'), [
        'nombre_completo' => 'Estudiante de prueba',
        'usuario' => 'estudiante-prueba',
        'email' => 'estudiante@example.test',
        'password' => 'password-seguro',
        'password_confirmation' => 'password-seguro',
        'rol' => 'Administrador',
    ])->assertRedirect(route('home'))
        ->assertSessionHas('status');

    $user = Usuario::where('email', 'estudiante@example.test')->firstOrFail();

    expect($user->rol)->toBe('Editor')
        ->and(Hash::check('password-seguro', $user->contrasena))->toBeTrue();

    $this->assertAuthenticatedAs($user);
    $this->get(route('admin.noticias.index'))->assertForbidden();
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Cerrar sesión');

    $this->post(route('logout'))
        ->assertRedirect(route('home'));

    $this->assertGuest();
});

test('login page displays a functional registration form', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('action="'.route('register').'"', false)
        ->assertSee('name="nombre_completo"', false)
        ->assertSee('name="password_confirmation"', false);
});

test('registration validates required and unique account fields', function () {
    Usuario::create([
        'nombre_completo' => 'Cuenta existente',
        'usuario' => 'cuenta-existente',
        'email' => 'existente@example.test',
        'contrasena' => Hash::make('password-seguro'),
        'rol' => 'Editor',
    ]);

    $this->from(route('login'))
        ->post(route('register', ['panel' => 'registro']), [
            'email' => 'existente@example.test',
            'usuario' => 'cuenta-existente',
            'nombre_completo' => 'Duplicado',
            'password' => 'password-seguro',
            'password_confirmation' => 'password-seguro',
        ])
        ->assertRedirect(route('login', ['panel' => 'registro']))
        ->assertSessionHasErrors(['email', 'usuario']);
});

test('new accounts default to the editor role', function () {
    $user = Usuario::create([
        'nombre_completo' => 'Usuario nuevo',
        'usuario' => 'usuario-nuevo',
        'email' => 'usuario@example.test',
        'contrasena' => Hash::make('password-seguro'),
    ]);

    expect($user->refresh()->rol)->toBe('Editor');
});

test('administrators can sign in to the administration panel', function () {
    Usuario::create([
        'nombre_completo' => 'Administrador de prueba',
        'usuario' => 'admin-prueba',
        'email' => 'admin@example.test',
        'contrasena' => Hash::make('password-seguro'),
        'rol' => 'Administrador',
    ]);

    $this->post(route('login'), [
        'email' => 'admin@example.test',
        'password' => 'password-seguro',
    ])->assertRedirect(route('admin.noticias.index'));

    $this->assertAuthenticatedAs(Usuario::where('email', 'admin@example.test')->first());
    $this->get(route('admin.noticias.index'))->assertOk();
});

test('editors cannot update the secret riddle', function () {
    $editor = new Usuario();
    $editor->forceFill(['rol' => 'Editor']);

    $this->actingAs($editor)
        ->post(route('admin.acertijo.store'), ['pregunta' => 'x', 'respuesta' => 'y'])
        ->assertForbidden();
});

test('editors cannot delete whispers', function () {
    $editor = new Usuario();
    $editor->forceFill(['rol' => 'Editor']);

    $this->actingAs($editor)
        ->delete(route('admin.susurro.destroy', 1))
        ->assertForbidden();
});

test('editors cannot read the contact inbox', function () {
    $editor = new Usuario();
    $editor->forceFill(['rol' => 'Editor']);

    $this->actingAs($editor)
        ->get(route('admin.mensajes.index'))
        ->assertForbidden();
});

test('guests are redirected to login for admin-only tools', function () {
    $this->post(route('admin.acertijo.store'))->assertRedirect(route('login'));
    $this->get(route('admin.mensajes.index'))->assertRedirect(route('login'));
});

test('administrators can update the secret riddle', function () {
    $admin = new Usuario();
    $admin->forceFill(['rol' => 'Administrador']);

    $this->actingAs($admin)
        ->post(route('admin.acertijo.store'), [
            'pregunta' => '¿Cuánto vale 2+2?',
            'respuesta' => 'cuatro',
        ])
        ->assertSessionHas('message');

    expect(\App\Models\Acertijo::latest('id_acertijo')->first()->respuesta)->toBe('cuatro');
});

test('administrators can delete whispers', function () {
    $susurro = \App\Models\Susurro::create([
        'apodo' => 'anonimo',
        'contenido' => 'Susurro de prueba',
        'categoria_viento' => 'campus',
        'soplos' => 0,
    ]);

    $admin = new Usuario();
    $admin->forceFill(['rol' => 'Administrador']);

    $this->actingAs($admin)
        ->delete(route('admin.susurro.destroy', $susurro->id_susurro))
        ->assertSessionHas('status');

    expect(\App\Models\Susurro::find($susurro->id_susurro))->toBeNull();
});