<?php

use Illuminate\Support\Facades\Route;
use Modules\Frontend\Http\Controllers\HomeController;
use Modules\Frontend\Http\Controllers\ProductController;
use Modules\Frontend\Http\Controllers\CartController;
use Modules\Frontend\Http\Controllers\CheckoutController;
use Modules\Frontend\Http\Controllers\LinktreeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rota da Página Inicial
Route::get('/', [HomeController::class, 'index'])->name('frontend.home');

// Rota da página de links (já existente)
Route::get('/links', [LinktreeController::class, 'show'])->name('linktree.show');

// Rotas de Produtos
Route::get('/produtos', [ProductController::class, 'index'])->name('frontend.products.index');
Route::get('/produto/{id}', [ProductController::class, 'show'])->name('frontend.products.show');

// Rotas do Carrinho (Funcionalidades)
Route::prefix('carrinho')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/adicionar', [CartController::class, 'add'])->name('add');
    Route::post('/atualizar/{id}', [CartController::class, 'update'])->name('update');
    Route::get('/remover/{id}', [CartController::class, 'remove'])->name('remove');
    Route::get('/limpar', [CartController::class, 'clear'])->name('clear');
});


// Rotas de Finalização de Compra
Route::get('/checkout', [CheckoutController::class, 'index'])->name('frontend.checkout.index');
Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('frontend.checkout.place-order');