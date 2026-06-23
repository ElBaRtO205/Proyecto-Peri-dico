<?php
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoticiaController;

Route::get('/', [NoticiaController::class, 'home']);
Route::resource('noticia', NoticiaController::class);


//  Ruta para mostrar la vista del formulario (Peticion GET)
Route::get('/login', function () {
    return view('login'); // la vista pa inciar sesion 
})->name('login');

// Ruta para procesar los datos que envia el formulario (Petición POST)
Route::post('/login', [LoginController::class, 'login']);

//  Ruta para cerrar sesion (por post)
Route::post('/logout', function () {
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');


// parte del login para la redireccion al panel de noticias
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);

// el panel de crud de noticias 
Route::resource('noticia', NoticiaController::class);

//para el boton de salir de modo admin en la vista del crud
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');