<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AuthController;
use Modules\Admin\Http\Controllers\DashboardController;
use Modules\Admin\Http\Controllers\UserController;
use Modules\Admin\Http\Controllers\ProductTypeController;
use Modules\Admin\Http\Controllers\ProductController;
use Modules\Admin\Http\Controllers\MiscCategoryController;
use Modules\Admin\Http\Controllers\MiscItemController;
use Modules\Admin\Http\Controllers\SupplierController;
use Modules\Admin\Http\Controllers\PurchaseController;
use Modules\Admin\Http\Controllers\StockController;
use Modules\Admin\Http\Controllers\PricingController;
use Modules\Admin\Http\Controllers\ClientController;
use Modules\Admin\Http\Controllers\SaleController;
use Modules\Admin\Http\Controllers\CashFlowController;
use Modules\Admin\Http\Controllers\FinancialTransactionController;
use Modules\Admin\Http\Controllers\FinancialCategoryController;
use Modules\Admin\Http\Controllers\LinktreeAdminController;
use Modules\Admin\Http\Controllers\ProductImageController;

Route::prefix('admin')->name('admin.')->group(function () {

     //
     // 1) ROTA DE LOGIN (ponto de entrada em /admin)
     //    — fique fora do auth:admin
     //
     Route::get('/', [AuthController::class, 'showLoginForm'])
          ->name('login');
     Route::post('login', [AuthController::class, 'login'])
          ->name('login.submit');

     //
     // 2) TODAS AS OUTRAS ROTAS DE ADMIN FICAM PROTEGIDAS
     //
     Route::middleware('auth:admin')->group(function () {

          // Dashboard 
          Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
          Route::get('dashboard/data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');

          // CRUD de usuários
          Route::get('users',           [UserController::class, 'index'])
               ->name('users.index');
          Route::get('users/create',    [UserController::class, 'create'])
               ->name('users.create');
          Route::post('users',           [UserController::class, 'store'])
               ->name('users.store');
          Route::get('users/{id}/edit', [UserController::class, 'edit'])
               ->name('users.edit');
          Route::put('users/{id}',      [UserController::class, 'update'])
               ->name('users.update');
          Route::delete('users/{id}',      [UserController::class, 'destroy'])
               ->name('users.destroy');


          // ROTAS PRODUTOS
          Route::get('product-types-list', [ProductTypeController::class, 'list'])->name('product-types.list');
          Route::get('product-types/data', [ProductTypeController::class, 'data'])->name('product-types.data');
          Route::get('products/data', [ProductController::class, 'data'])->name('products.data');
          Route::resource('product-types', ProductTypeController::class);
          Route::resource('products', ProductController::class);

          // ROTAS PARA CLIENTES
          Route::get('clients/data', [ClientController::class, 'data'])->name('clients.data');
          Route::resource('clients', ClientController::class);

          // ROTAS PARA ITENS DIVERSOS
          Route::get('misc/categories/data', [MiscCategoryController::class, 'data'])->name('misc-categories.data');
          Route::get('misc/items/data', [MiscItemController::class, 'data'])->name('misc-items.data');
          Route::resource('misc-categories', MiscCategoryController::class);
          Route::resource('misc-items', MiscItemController::class);
          Route::get('misc-categories-list', [MiscCategoryController::class, 'list'])->name('misc-categories.list');

          // ROTAS PARA FORNECEDORES
          Route::get('suppliers/data', [SupplierController::class, 'data'])->name('suppliers.data');
          Route::resource('suppliers', SupplierController::class);

          // ROTAS PARA COMPRAS (CONTAS A PAGAR)
          Route::get('purchases/data', [PurchaseController::class, 'data'])->name('purchases.data');
          Route::resource('purchases', PurchaseController::class);
          // ROTA PARA BUSCAR DETALHES DE UMA COMPRA (NOVO)
          Route::get('purchases/{id}/details', [PurchaseController::class, 'details'])->name('purchases.details');

          // ROTA PARA ATUALIZAR APENAS O STATUS DE PAGAMENTO (Modificada)
          Route::post('purchases/{id}/update-payment-status', [PurchaseController::class, 'updatePaymentStatus'])->name('purchases.update-payment-status');

          // ROTA PARA REGISTRAR O RECEBIMENTO DOS ITENS (NOVO)
          Route::post('purchases/{id}/receive', [PurchaseController::class, 'receiveItems'])->name('purchases.receive');
          Route::get('purchases/{id}/items', [PurchaseController::class, 'showItems'])->name('purchases.show-items');

          // A rota resource principal
          Route::resource('purchases', PurchaseController::class);


          // ROTAS PARA O CONTROLE DE ESTOQUE
          Route::get('stock', [StockController::class, 'index'])->name('stock.index');
          Route::get('stock/product-data', [StockController::class, 'productStockData'])->name('stock.product-data');
          Route::get('stock/misc-item-data', [StockController::class, 'miscItemStockData'])->name('stock.misc-item-data');
          Route::post('stock/update-minimum', [StockController::class, 'updateMinimumStock'])->name('stock.update-minimum');


          // ROTAS PARA PRECIFICAÇÃO (ORDEM CORRIGIDA)
          Route::get('pricing', [PricingController::class, 'index'])->name('pricing.index');
          Route::get('pricing/data', [PricingController::class, 'data'])->name('pricing.data');
          // Rota específica vem PRIMEIRO
          Route::post('pricing/settings', [PricingController::class, 'updateSettings'])->name('pricing.settings.update');
          // Rota com parâmetro vem DEPOIS e com método corrigido
          Route::post('pricing/{id}', [PricingController::class, 'updateSalePrice'])->name('pricing.update');


          // ROTAS PARA VENDAS
          Route::get('sales/data', [SaleController::class, 'data'])->name('sales.data');
          Route::get('sales/search-products', [SaleController::class, 'searchProducts'])->name('sales.search-products');
          Route::get('sales/search-misc-items', [SaleController::class, 'searchMiscItems'])->name('sales.search-misc-items');
          Route::resource('sales', SaleController::class)->except(['edit', 'update']); // Vamos focar em criar e visualizar primeiro
          Route::post('sales/{id}/update-status', [SaleController::class, 'updateStatus'])->name('sales.update-status');

          // ROTAS PARA FLUXO DE CAIXA
          Route::get('cash-flow', [CashFlowController::class, 'index'])->name('cash-flow.index');
          Route::get('cash-flow/report', [CashFlowController::class, 'generateReport'])->name('cash-flow.report');

          // ROTAS PARA LANÇAMENTOS FINANCEIROS
          Route::get('financial-transactions/data', [FinancialTransactionController::class, 'data'])->name('financial-transactions.data');
          Route::resource('financial-transactions', FinancialTransactionController::class);

          // Logout
          Route::post('logout', [AuthController::class, 'logout'])
               ->name('logout');

          // NOVO: ROTAS PARA CATEGORIAS FINANCEIRAS
          Route::prefix('financial-categories')->name('financial-categories.')->group(function () {
               Route::get('/', [FinancialCategoryController::class, 'index'])->name('index');
               Route::post('/', [FinancialCategoryController::class, 'store'])->name('store');
               Route::put('/{category}', [FinancialCategoryController::class, 'update'])->name('update');
               Route::delete('/{category}', [FinancialCategoryController::class, 'destroy'])->name('destroy');
          });


          // ROTAS PARA GERENCIAR IMAGENS DE PRODUTOS
          Route::prefix('products/{product}/images')->name('products.images.')->group(function () {
               Route::get('/', [ProductImageController::class, 'index'])->name('index');
               Route::post('/', [ProductImageController::class, 'store'])->name('store');
          });
          Route::delete('/product-images/{image}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');
          Route::post('/product-images/update-order', [ProductImageController::class, 'updateOrder'])->name('products.images.update-order');
          // ROTAS PARA GERENCIAR O LINKTREE
          Route::prefix('linktree-manager')->name('linktree-manager.')->group(function () {
               Route::get('/', [LinktreeAdminController::class, 'index'])->name('index');
               Route::get('/data', [LinktreeAdminController::class, 'data'])->name('data');
               Route::post('/store', [LinktreeAdminController::class, 'store'])->name('store');
               Route::put('/{link}', [LinktreeAdminController::class, 'update'])->name('update');
               Route::delete('/{link}', [LinktreeAdminController::class, 'destroy'])->name('destroy');
               Route::post('/update-order', [LinktreeAdminController::class, 'updateOrder'])->name('update-order');
               Route::post('/settings', [LinktreeAdminController::class, 'updateSettings'])->name('settings.update');
               Route::get('socials/data', [LinktreeAdminController::class, 'socialData'])
                    ->name('socials.data');
               Route::post('socials', [LinktreeAdminController::class, 'storeSocial'])
                    ->name('socials.store');
               Route::put('socials/{id}', [LinktreeAdminController::class, 'updateSocial'])
                    ->name('socials.update');
               Route::delete('socials/{id}', [LinktreeAdminController::class, 'destroySocial'])
                    ->name('socials.destroy');
               Route::post('socials/order', [LinktreeAdminController::class, 'updateSocialOrder'])
                    ->name('socials.order');
          });
     });
});
