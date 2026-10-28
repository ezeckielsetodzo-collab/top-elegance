@extends('layouts.app')

@section('title', 'Test des composants · TOP ÉLÉGANCE')

@section('content')
@php
    $produits = [
        ['name' => 'TE Signature', 'tagline' => 'Sobre, élégant, intemporel', 'color' => '#161616', 'preorder_open' => true, 'is_limited' => false],
        ['name' => 'Lomé Edition', 'tagline' => 'Racines, fierté, Togo', 'color' => '#f4f1ea', 'preorder_open' => false, 'is_limited' => false],
        ['name' => 'Founder Edition', 'tagline' => 'Exclusivité, prestige, vision', 'color' => '#0e0e0e', 'preorder_open' => false, 'is_limited' => true],
    ];
@endphp

<div class="mx-auto max-w-6xl px-5 py-12">
    <h1 class="font-serif text-4xl font-extrabold">Composants</h1>

    <h2 class="mt-10 mb-3 font-serif text-2xl">Boutons</h2>
    <div class="flex flex-wrap gap-3">
        <x-button>Principal</x-button>
        <x-button variant="secondary">Secondaire</x-button>
        <x-button disabled>Désactivé</x-button>
    </div>

    <h2 class="mt-10 mb-3 font-serif text-2xl">Badges</h2>
    <div class="flex flex-wrap gap-3">
        <x-badge type="preorder" />
        <x-badge type="soon" />
        <x-badge type="limited" />
    </div>

    <h2 class="mt-10 mb-3 font-serif text-2xl">Choix de taille</h2>
    <x-size-selector />

    <h2 class="mt-10 mb-3 font-serif text-2xl">Cartes produit</h2>
    <div class="grid grid-cols-2 gap-4 md:grid-cols-3">
        @foreach ($produits as $produit)
            <x-product-card :product="$produit" />
        @endforeach
    </div>
</div>
@endsection