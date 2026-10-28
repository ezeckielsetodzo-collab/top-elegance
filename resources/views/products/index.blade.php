@extends('layouts.app')

@section('title', 'Collection DROP 01 · TOP ÉLÉGANCE')
@section('description', 'Les dix modèles du DROP 01 de TOP ÉLÉGANCE : streetwear élégant, séries limitées, précommande ouverte.')

@section('content')
@php
    $products = $products ?? \App\Support\ExempleProduits::tous();
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