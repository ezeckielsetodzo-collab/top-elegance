<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/collection', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/modeles/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');