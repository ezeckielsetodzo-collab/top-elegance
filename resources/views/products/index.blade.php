@extends('layouts.app')

@section('title', 'Collection DROP 01 · TOP ÉLÉGANCE')
@section('description', 'Les dix modèles du DROP 01 de TOP ÉLÉGANCE : streetwear élégant, séries limitées, précommande ouverte.')

@section('content')
@php
    // Données d'exemple : le Dev 2 enverra les vrais produits via $products.
    $products = $products ?? [
        ['name' => 'TE Signature', 'tagline' => 'Sobre, élégant, intemporel', 'color' => '#161616', 'colors' => ['Noir', 'Blanc', 'Gris'], 'preorder_open' => true, 'is_limited' => false],
        ['name' => 'Lomé Edition', 'tagline' => 'Racines, fierté, Togo', 'color' => '#f4f1ea', 'colors' => ['Blanc', 'Noir', 'Beige'], 'preorder_open' => false, 'is_limited' => false],
        ['name' => 'Black Luxury', 'tagline' => 'Élégance, minimaliste, premium', 'color' => '#101010', 'colors' => ['Noir', 'Blanc', 'Doré'], 'preorder_open' => true, 'is_limited' => false],
        ['name' => 'Oversize Royal', 'tagline' => 'Confort, modernité, caractère', 'color' => '#d9c4a0', 'colors' => ['Beige', 'Noir'], 'preorder_open' => false, 'is_limited' => false],
        ['name' => 'African Elegance', 'tagline' => 'Culture, identité, modernité', 'color' => '#141414', 'colors' => ['Noir', 'Blanc', 'Vert'], 'preorder_open' => false, 'is_limited' => false],
        ['name' => 'TE 01', 'tagline' => 'Ambition, progression, évolution', 'color' => '#8a8a88', 'colors' => ['Gris', 'Noir', 'Blanc'], 'preorder_open' => false, 'is_limited' => true],
        ['name' => 'No Rules', 'tagline' => 'Liberté, style, élégance', 'color' => '#f4f1ea', 'colors' => ['Blanc', 'Noir', 'Gris'], 'preorder_open' => false, 'is_limited' => false],
        ['name' => 'Minimal TE', 'tagline' => 'Simple, classique, efficace', 'color' => '#101010', 'colors' => ['Noir', 'Blanc', 'Gris'], 'preorder_open' => false, 'is_limited' => false],
        ['name' => '228 Edition', 'tagline' => 'Togo, unité, fierté', 'color' => '#0f4a2c', 'colors' => ['Vert', 'Blanc', 'Noir'], 'preorder_open' => true, 'is_limited' => false],
        ['name' => 'Founder Edition', 'tagline' => 'Exclusivité, prestige, vision', 'color' => '#0e0e0e', 'colors' => ['Noir', 'Doré', 'Blanc'], 'preorder_open' => false, 'is_limited' => true],
    ];
    $filters = ['Tous', 'Noir', 'Blanc', 'Gris', 'Beige', 'Vert', 'Doré'];
@endphp

<div class="mx-auto max-w-6xl px-5 py-12"
     x-data="{
        filter: 'Tous',
        produits: @js($products),
        match(p) { return this.filter === 'Tous' || (p.colors || []).includes(this.filter) },
        get total() { return this.produits.filter(p => this.match(p)).length }
     }">

    <h1 class="font-serif text-4xl font-extrabold md:text-5xl">Collection DROP 01</h1>
    <p class="mt-3 max-w-xl text-sourdine">Dix modèles, une seule vision. Filtrez par couleur.</p>

    <div class="mt-8 flex flex-wrap gap-2" role="group" aria-label="Filtrer par couleur">
        @foreach ($filters as $f)
            <button type="button"
                    @click="filter = '{{ $f }}'"
                    :aria-pressed="filter === '{{ $f }}'"
                    class="rounded-full border px-4 py-1.5 text-sm transition"
                    :class="filter === '{{ $f }}' ? 'border-transparent bg-or text-noir' : 'border-ligne text-sourdine hover:text-ivoire'">
                {{ $f }}
            </button>
        @endforeach
    </div>

    <p class="mt-6 text-sm text-sourdine" aria-live="polite"
       x-text="total + (total > 1 ? ' modèles' : ' modèle')"></p>

    <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-5">
        @foreach ($products as $product)
            <div class="grid" x-show="match(produits[{{ $loop->index }}])">
                <x-product-card :product="$product" />
            </div>
        @endforeach
    </div>

    <p x-cloak x-show="total === 0" class="mt-8 text-sourdine">Aucun modèle dans cette couleur pour le moment.</p>
</div>
@endsection