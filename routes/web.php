<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');
Route::get('/collection', fn () => view('products.index'))->name('products.index');
Route::get('/modeles/{slug}', function (string $slug) {
    $product = \App\Support\ExempleProduits::trouver($slug);
    abort_if(! $product, 404);

    return view('products.show', ['product' => $product]);
})->name('products.show');