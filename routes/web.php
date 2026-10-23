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