<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');
Route::view('/la-marque', 'pages.brand')->name('brand');
Route::view('/conditions-de-vente', 'pages.terms')->name('terms');

Route::get('/collection', [ProductController::class, 'index'])->name('products.index');
Route::get('/modeles/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/precommander', [OrderController::class, 'create'])->name('orders.create');
Route::post('/precommander', [OrderController::class, 'store'])->middleware('throttle:10,1')->name('orders.store');
Route::get('/sondage/{product}', [PollController::class, 'results'])->name('poll.results');
Route::post('/sondage', [PollController::class, 'vote'])->middleware('throttle:20,1')->name('poll.vote');
Route::get('/precommande/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/connexion', [AdminAuthController::class, 'create'])->name('login');
    Route::post('/connexion', [AdminAuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');

    Route::middleware(EnsureUserIsAdmin::class)->group(function () {
        Route::post('/deconnexion', [AdminAuthController::class, 'destroy'])->name('logout');
        Route::get('/commandes', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/produits', [\App\Http\Controllers\Admin\AdminProductController::class, 'index'])->name('products.index');
        Route::patch('/produits/{product}', [\App\Http\Controllers\Admin\AdminProductController::class, 'update'])->name('products.update');
        Route::get('/stock', [\App\Http\Controllers\Admin\AdminStockController::class, 'index'])->name('stock.index');
        Route::get('/stock/{product}', [\App\Http\Controllers\Admin\AdminStockController::class, 'edit'])->name('stock.edit');
        Route::put('/stock/{product}', [\App\Http\Controllers\Admin\AdminStockController::class, 'update'])->name('stock.update');
        Route::get('/ambassadeurs', [\App\Http\Controllers\Admin\AdminAmbassadorController::class, 'index'])->name('ambassadors.index');
        Route::post('/ambassadeurs', [\App\Http\Controllers\Admin\AdminAmbassadorController::class, 'store'])->name('ambassadors.store');
        Route::patch('/ambassadeurs/{ambassador}', [\App\Http\Controllers\Admin\AdminAmbassadorController::class, 'update'])->name('ambassadors.update');
        Route::get('/finances', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'index'])->name('finances.index');
        Route::post('/finances', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'store'])->name('finances.store');
        Route::delete('/finances/{entry}', [\App\Http\Controllers\Admin\AdminFinanceController::class, 'destroy'])->name('finances.destroy');
        Route::get('/indicateurs', [\App\Http\Controllers\Admin\AdminIndicatorController::class, 'index'])->name('indicators.index');
        Route::get('/commandes/{order}/bon', [\App\Http\Controllers\Admin\AdminDocumentController::class, 'order'])->name('documents.order');
        Route::get('/commandes/{order}/livraison', [\App\Http\Controllers\Admin\AdminDocumentController::class, 'delivery'])->name('documents.delivery');
        Route::get('/ambassadeurs/{ambassador}/fiche', [\App\Http\Controllers\Admin\AdminDocumentController::class, 'ambassador'])->name('documents.ambassador');
        Route::get('/indicateurs/export', [\App\Http\Controllers\Admin\AdminExportController::class, 'indicators'])->name('exports.indicators');
        Route::get('/commandes/export', [\App\Http\Controllers\Admin\AdminExportController::class, 'orders'])->name('exports.orders');
        Route::get('/sondage', [\App\Http\Controllers\Admin\AdminPollController::class, 'index'])->name('polls.index');
        Route::get('/clients', [\App\Http\Controllers\Admin\AdminCustomerController::class, 'index'])->name('customers.index');
        Route::patch('/commandes/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
    });
});
