<?php

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

/**
 * Crea un usuario con el rol indicado.
 */
function crearUsuarioPrueba(string $rol): Usuario
{
    return Usuario::create([
        'nombre_completo' => 'Usuario de prueba',
        'usuario' => 'usuario-' . strtolower($rol) . '-' . uniqid(),
        'email' => strtolower($rol) . '-' . uniqid() . '@example.test',
        'contrasena' => Hash::make('password-seguro'),
        'rol' => $rol,
    ]);
}

test('the admin panel keeps light text on the dark soplar and mensajes buttons', function () {
    $admin = crearUsuarioPrueba('Administrador');

    $this->actingAs($admin)
        ->get(route('admin.noticias.index'))
        ->assertOk()
        // Los dos botones con fondo oscuro deben usar la clase de texto claro
        ->assertSee('panel-admin__btn-nueva--claro', false)
        ->assertSee(route('secreto.index'), false)
        ->assertSee(route('admin.mensajes.index'), false);
});

test('the whisper page shows a back to admin panel link for admins', function () {
    $admin = crearUsuarioPrueba('Administrador');

    $this->actingAs($admin)
        ->get(route('secreto.index'))
        ->assertOk()
        ->assertSee('Volver al panel de admin')
        ->assertSee(route('admin.noticias.index'), false);
});

test('regular visitors do not see the admin panel link on the whisper page', function () {
    // Un Editor no es admin: no debe ver el enlace al panel
    $editor = crearUsuarioPrueba('Editor');

    // El editor ni siquiera accede sin resolver el acertijo → lo mandamos a home
    $this->actingAs($editor)
        ->get(route('secreto.index'))
        ->assertRedirect(route('home'));
});

test('the header logo and name link back to the home page', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('class="encabezado__enlace-home"', false)
        ->assertSee('href="'.route('home').'"', false);
});
