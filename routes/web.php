<?php

use Illuminate\Support\Facades\Route;
// Importa o controller da sua loja
use Modules\Frontend\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui é onde a rota principal da aplicação é registrada.
|
*/

// Agora, a rota raiz (/) aponta para a página inicial da sua loja
Route::get('/', [HomeController::class, 'index'])->name('frontend.home');