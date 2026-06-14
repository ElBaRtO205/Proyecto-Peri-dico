<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NoticiaController;

Route::get('/', [NoticiaController::class, 'home']);
Route::resource('noticia', NoticiaController::class);