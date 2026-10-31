<?php

use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\SoplarAlVientoController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Autenticación
|--------------------------------------------------------------------------
*/
Route::get('/login', fn () => view('auth.login'))->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/register', [LoginController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('register');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Portada y noticias (públicas)
|--------------------------------------------------------------------------
*/
Route::get('/', [NoticiaController::class, 'home'])->name('home');
Route::get('/categoria/{id}', [NoticiaController::class, 'categoria'])->name('noticia.categoria')->whereNumber('id');

/*
|--------------------------------------------------------------------------
| Feeds y SEO (RSS + sitemap)
|--------------------------------------------------------------------------
*/
Route::get('/feed.xml', [FeedController::class, 'rss'])->name('feed.rss');
Route::get('/sitemap.xml', [FeedController::class, 'sitemap'])->name('sitemap');

/*
|--------------------------------------------------------------------------
| Comentarios de las noticias
|--------------------------------------------------------------------------
*/
Route::post('/noticia/{id}/comentario', [ComentarioController::class, 'store'])
    ->middleware('throttle:10,1')
    ->whereNumber('id')
    ->name('comentarios.store');

/*
|--------------------------------------------------------------------------
| Contacto y buzón de sugerencias (solo enlazado desde el footer)
|--------------------------------------------------------------------------
*/
Route::get('/contacto', [ContactoController::class, 'index'])->name('contacto.index');
Route::post('/contacto', [ContactoController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('contacto.store');

/*
|--------------------------------------------------------------------------
| Soplar al Viento (rincón secreto del campus)
|--------------------------------------------------------------------------
*/
Route::post('/soplar-al-viento/desbloquear', [SoplarAlVientoController::class, 'desbloquear'])
    ->middleware('throttle:10,1')
    ->name('secreto.desbloquear');
Route::post('/soplar-al-viento/publicar', [SoplarAlVientoController::class, 'storeSusurro'])
    ->middleware('throttle:5,1')
    ->name('secreto.publicar');
Route::post('/soplar-al-viento/{id}/soplar', [SoplarAlVientoController::class, 'soplarSusurro'])
    ->whereNumber('id')
    ->name('secreto.soplar');
Route::get('/soplar-al-viento', [SoplarAlVientoController::class, 'index'])->name('secreto.index');

/*
|--------------------------------------------------------------------------
| Panel de administración (CRUD de noticias)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('noticia', NoticiaController::class)
        ->names('admin.noticias')
        ->except(['show']);
});

// IMPORTANTE: esta ruta pública se define DESPUÉS del resource para que
// "/noticia/create" (y "/noticia/{id}/edit") las resuelva primero el panel
// admin. Si se registrara antes, capturaría "/noticia/create" como un slug.
Route::get('/noticia/{slug}', [NoticiaController::class, 'show'])
    ->where('slug', '[a-zA-Z0-9\-]+')
    ->name('noticia.show');

/*
|--------------------------------------------------------------------------
| Herramientas exclusivas del administrador
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', EnsureUserIsAdmin::class])->group(function () {
    Route::post('/admin/acertijo', [SoplarAlVientoController::class, 'actualizarAcertijo'])->name('admin.acertijo.store');
    Route::delete('/admin/susurro/{id}', [SoplarAlVientoController::class, 'destroySusurro'])->name('admin.susurro.destroy');

    Route::get('/admin/mensajes', [ContactoController::class, 'adminIndex'])->name('admin.mensajes.index');
    Route::patch('/admin/mensajes/{mensaje}/leido', [ContactoController::class, 'toggleLeido'])->name('admin.mensajes.leido');
    Route::delete('/admin/mensajes/{mensaje}', [ContactoController::class, 'destroy'])->name('admin.mensajes.destroy');
});
