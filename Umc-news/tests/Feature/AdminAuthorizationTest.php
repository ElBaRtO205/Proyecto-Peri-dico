<?php

use App\Models\Acertijo;
use App\Models\Susurro;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('guests are redirected to login when accessing news management', function () {
    $this->get(route('admin.noticias.index'))
        ->assertRedirect(route('login'));
});

test('lectors cannot access news management', function () {
    $lector = new Usuario;
    $lector->forceFill(['rol' => 'Lector']);

    $this->actingAs($lector)
        ->get(route('admin.noticias.index'))
        ->assertForbidden();
});

test('lectors can sign in but cannot access news management', function () {
    Usuario::create([
        'nombre_completo' => 'Lector de prueba',
        'usuario' => 'lector-prueba',
        'email' => 'lector@example.test',
        'contrasena' => Hash::make('password-seguro'),
        'rol' => 'Lector',
    ]);

    $this->post(route('login'), [
        'email' => 'lector@example.test',
        'password' => 'password-seguro',
    ])->assertRedirect(route('home'));

    $this->assertAuthenticatedAs(Usuario::where('email', 'lector@example.test')->first());

    $this->get(route('admin.noticias.index'))->assertForbidden();
});

test('registration creates and signs in a lector account', function () {
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

    expect($user->rol)->toBe('Lector')
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
        'rol' => 'Lector',
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

test('new accounts default to the lector role', function () {
    $user = Usuario::create([
        'nombre_completo' => 'Usuario nuevo',
        'usuario' => 'usuario-nuevo',
        'email' => 'usuario@example.test',
        'contrasena' => Hash::make('password-seguro'),
    ]);

    expect($user->refresh()->rol)->toBe('Lector');
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

test('lectors cannot update the secret riddle', function () {
    $lector = new Usuario;
    $lector->forceFill(['rol' => 'Lector']);

    $this->actingAs($lector)
        ->post(route('admin.acertijo.store'), ['pregunta' => 'x', 'respuesta' => 'y'])
        ->assertForbidden();
});

test('lectors cannot delete whispers', function () {
    $lector = new Usuario;
    $lector->forceFill(['rol' => 'Lector']);

    $this->actingAs($lector)
        ->delete(route('admin.susurro.destroy', 1))
        ->assertForbidden();
});

test('lectors cannot wipe the whisper section', function () {
    $lector = new Usuario;
    $lector->forceFill(['rol' => 'Lector']);

    $this->actingAs($lector)
        ->delete(route('admin.secreto.limpiar'))
        ->assertForbidden();
});

test('lectors cannot read the contact inbox', function () {
    $lector = new Usuario;
    $lector->forceFill(['rol' => 'Lector']);

    $this->actingAs($lector)
        ->get(route('admin.mensajes.index'))
        ->assertForbidden();
});

test('guests are redirected to login for admin-only tools', function () {
    $this->post(route('admin.acertijo.store'))->assertRedirect(route('login'));
    $this->delete(route('admin.secreto.limpiar'))->assertRedirect(route('login'));
    $this->get(route('admin.mensajes.index'))->assertRedirect(route('login'));
});

test('administrators can update the secret riddle', function () {
    $admin = new Usuario;
    $admin->forceFill(['rol' => 'Administrador']);

    $this->actingAs($admin)
        ->post(route('admin.acertijo.store'), [
            'pregunta' => '¿Cuánto vale 2+2?',
            'respuesta' => 'cuatro',
        ])
        ->assertSessionHas('message');

    expect(Acertijo::latest('id_acertijo')->first()->respuesta)->toBe('cuatro');
});

test('administrators can delete whispers', function () {
    $susurro = Susurro::create([
        'apodo' => 'anonimo',
        'contenido' => 'Susurro de prueba',
        'categoria_viento' => 'campus',
        'soplos' => 0,
    ]);

    $admin = new Usuario;
    $admin->forceFill(['rol' => 'Administrador']);

    $this->actingAs($admin)
        ->delete(route('admin.susurro.destroy', $susurro->id_susurro))
        ->assertSessionHas('status');

    expect(Susurro::find($susurro->id_susurro))->toBeNull();
});

test('administrators can wipe all whispers, riddles and access keys', function () {
    $acertijo = Acertijo::create([
        'pregunta' => '¿Cuál es la clave?',
        'respuesta' => 'brisa',
        'activo' => true,
    ]);

    Susurro::create([
        'apodo' => 'anonimo',
        'contenido' => 'Susurro que debe desaparecer',
        'categoria_viento' => 'Confesión',
        'soplos' => 0,
    ]);

    $admin = new Usuario;
    $admin->forceFill(['rol' => 'Administrador']);

    $this->actingAs($admin)
        ->withSession([
            'acertijo_resuelto_id' => $acertijo->id_acertijo,
            'soplado_1' => true,
        ])
        ->delete(route('admin.secreto.limpiar'))
        ->assertRedirect(route('secreto.index'))
        ->assertSessionHas('status')
        ->assertSessionMissing('acertijo_resuelto_id')
        ->assertSessionMissing('soplado_1');

    expect(Susurro::count())->toBe(0)
        ->and(Acertijo::count())->toBe(0);
});
