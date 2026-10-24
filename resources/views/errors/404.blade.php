@extends('layouts.app')

@section('title', 'Page introuvable · TOP ÉLÉGANCE')

@section('content')
<div class="mx-auto max-w-xl px-5 py-24 text-center">
    <p class="font-serif text-8xl font-extrabold text-or">404</p>
    <h1 class="mt-4 font-serif text-3xl font-extrabold">Cette page est introuvable.</h1>
    <p class="mt-3 text-sourdine">Le lien est peut-être erroné, ou la page n’existe plus.</p>
    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <x-button href="{{ url('/') }}">Retour à l’accueil</x-button>
        <x-button href="{{ route('products.index') }}" variant="secondary">Voir la collection</x-button>
    </div>
</div>
@endsection