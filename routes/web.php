<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/collection', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/modeles/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
Route::get('/precommande', [\App\Http\Controllers\OrderController::class, 'create'])->name('orders.create');
Route::post('/precommande', [\App\Http\Controllers\OrderController::class, 'store'])->middleware('throttle:10,1')->name('orders.store');
Route::get('/precommande/confirmation', [\App\Http\Controllers\OrderController::class, 'confirmation'])->name('orders.confirmation');
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/connexion', [\App\Http\Controllers\Admin\AdminAuthController::class, 'create'])->name('login');
    Route::post('/connexion', [\App\Http\Controllers\Admin\AdminAuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');

    Route::middleware(\App\Http\Middleware\EnsureUserIsAdmin::class)->group(function () {
        Route::post('/deconnexion', [\App\Http\Controllers\Admin\AdminAuthController::class, 'destroy'])->name('logout');
        Route::get('/commandes', [\App\Http\Controllers\Admin\AdminOrderController::class, 'index'])->name('orders.index');
        Route::patch('/commandes/{order}', [\App\Http\Controllers\Admin\AdminOrderController::class, 'update'])->name('orders.update');
    });
});