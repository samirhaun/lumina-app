<?php

use Illuminate\Support\Facades\Route;
use Modules\Vendas\Http\Controllers\VendasController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('vendas', VendasController::class)->names('vendas');
});
