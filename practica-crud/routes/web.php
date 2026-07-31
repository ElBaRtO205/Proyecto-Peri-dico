<?php

use App\Http\Controllers\ComentarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NoticiaController;
use App\Http\Controllers\UsuarioController;

// --- RUTAS PÚBLICAS ---
Route::get('/', [NoticiaController::class, 'home'])->name('home');
Route::get('/categoria/{id}', [NoticiaController::class, 'categoria'])->name('noticia.categoria');
Route::get('/noticia/{id}', [NoticiaController::class, 'show'])->name('noticia.show')->whereNumber('id');

// --- AUTENTICACIÓN ---
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// --- PANEL DE ADMINISTRACIÓN (CRUD) ---
Route::resource('noticia', NoticiaController::class)->names('admin.noticias')->except(['show']);

// --- Comentarios De Las Noticias ---
Route::post('/noticia/{id}/comentario', [ComentarioController::class, 'store'])->name('comentarios.store');