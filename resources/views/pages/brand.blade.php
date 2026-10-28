@extends('layouts.app')

@section('title', 'La marque · TOP ÉLÉGANCE')
@section('description', 'TOP ÉLÉGANCE mélange streetwear, minimalisme et références togolaises : des pièces faciles à porter, immédiatement reconnaissables.')

@section('content')
<div class="mx-auto max-w-3xl px-5 py-16">
    <h1 class="font-serif text-4xl font-extrabold md:text-5xl">L’élégance commence par soi-même.</h1>
    <p class="mt-6 text-lg text-sourdine">
        TOP ÉLÉGANCE est une marque jeune, moderne et accessible, née à Lomé. Le DROP 01 mélange streetwear,
        minimalisme et références togolaises pour créer des pièces faciles à porter, mais immédiatement reconnaissables.
    </p>

    <div class="mt-12 grid gap-8 sm:grid-cols-2">
        <div>
            <h2 class="font-serif text-2xl font-semibold">Qualité premium</h2>
            <p class="mt-2 text-sourdine">Un coton confortable et suffisamment épais pour garder une belle tenue, des coutures et des logos contrôlés avant chaque emballage.</p>
        </div>
        <div>
            <h2 class="font-serif text-2xl font-semibold">Finitions soignées</h2>
            <p class="mt-2 text-sourdine">Le monogramme TE est brodé pour un rendu premium, les grands motifs sont imprimés avec soin, et chaque pièce porte une étiquette TOP ÉLÉGANCE.</p>
        </div>
        <div>
            <h2 class="font-serif text-2xl font-semibold">Éditions limitées</h2>
            <p class="mt-2 text-sourdine">De petites séries, certaines numérotées, pour rester rares et tester la demande avant de produire davantage.</p>
        </div>
        <div>
            <h2 class="font-serif text-2xl font-semibold">Fierté togolaise</h2>
            <p class="mt-2 text-sourdine">Lomé, 228, des couleurs et des motifs d’ici, utilisés avec sobriété pour garder une image élégante.</p>
        </div>
    </div>

    <p class="mt-14 border-t border-ligne pt-8 font-serif text-xl italic text-or">Style · Confiance · Élégance</p>
    <div class="mt-8 flex flex-wrap gap-3">
        <x-button href="{{ route('products.index') }}">Voir la collection</x-button>
        <x-button href="{{ route('orders.create') }}" variant="secondary">Précommander</x-button>
    </div>
</div>
@endsection