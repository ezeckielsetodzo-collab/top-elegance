<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'))->name('home');
Route::get('/collection', fn () => view('products.index'))->name('products.index');
Route::get('/modeles/{slug}', function (string $slug) {
    $product = \App\Support\ExempleProduits::trouver($slug);
    abort_if(! $product, 404);

    return view('products.show', ['product' => $product]);
})->name('products.show');
// ROUTES PROVISOIRES de précommande : le Dev 2 les remplacera par OrderController.
Route::get('/precommander', function (\Illuminate\Http\Request $request) {
    return view('orders.create', [
        'ouverts' => collect(\App\Support\ExempleProduits::tous())->where('preorder_open', true)->values()->all(),
        'choix' => $request->only(['modele', 'couleur']),
    ]);
})->name('orders.create');

Route::post('/precommander', function (\Illuminate\Http\Request $request) {
    $ouverts = collect(\App\Support\ExempleProduits::tous())->where('preorder_open', true)->pluck('slug')->all();

    $data = $request->validate([
        'nom' => ['required', 'string', 'max:100'],
        'telephone' => ['required', 'regex:/^[0-9 +().-]{8,20}$/'],
        'quartier' => ['required', 'string', 'max:150'],
        'modele' => ['required', \Illuminate\Validation\Rule::in($ouverts)],
        'couleur' => ['required', 'string', 'max:30'],
        'taille' => ['required', \Illuminate\Validation\Rule::in(['S', 'M', 'L', 'XL'])],
        'quantite' => ['required', 'integer', 'min:1', 'max:10'],
        'consentement' => ['accepted'],
        'cgv' => ['accepted'],
        'site_web' => ['nullable', 'max:0'],
    ], [
        'required' => 'Ce champ est obligatoire.',
        'regex' => 'Entrez un numéro de téléphone valide.',
        'in' => 'Ce choix n’est pas disponible.',
        'accepted' => 'Vous devez cocher cette case pour continuer.',
        'integer' => 'Entrez un nombre entier.',
        'min' => 'La valeur est trop petite.',
        'max' => 'La valeur est trop grande.',
    ]);

    $produit = \App\Support\ExempleProduits::trouver($data['modele']);

    session()->put('commande', [
        'nom' => $data['nom'],
        'telephone' => $data['telephone'],
        'quartier' => $data['quartier'],
        'modele' => $produit['name'],
        'couleur' => $data['couleur'],
        'taille' => $data['taille'],
        'quantite' => $data['quantite'],
    ]);

    return redirect()->route('orders.confirmation');
})->middleware('throttle:10,1')->name('orders.store');

Route::get('/precommande/confirmation', function () {
    $commande = session('commande');

    if (! $commande) {
        return redirect()->route('products.index');
    }

    return view('orders.confirmation', ['commande' => $commande]);
})->name('orders.confirmation');