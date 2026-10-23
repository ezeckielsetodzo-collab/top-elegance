@extends('layouts.app')

@section('title', 'Précommande reçue · TOP ÉLÉGANCE')

@section('content')
@php
    $numero = config('shop.whatsapp_number', ''); // à fournir par le Dev 2 / le propriétaire
    $message = 'Bonjour TOP ÉLÉGANCE, je viens de précommander le modèle '.$commande['modele']
        .' ('.$commande['couleur'].', taille '.$commande['taille'].', quantité '.$commande['quantite'].'). Nom : '.$commande['nom'].'.';
@endphp

<div class="mx-auto max-w-xl px-5 py-16 text-center">
    <p class="font-serif text-5xl text-or" aria-hidden="true">✓</p>
    <h1 class="mt-4 font-serif text-4xl font-extrabold">Merci {{ $commande['nom'] }} !</h1>
    <p class="mt-3 text-sourdine">Votre précommande est bien reçue. Nous vous confirmons le prix, le délai et le paiement par WhatsApp.</p>

    <dl class="mx-auto mt-8 max-w-sm divide-y divide-ligne rounded border border-ligne bg-noir-doux text-left">
        <div class="flex justify-between px-4 py-3"><dt class="text-sourdine">Modèle</dt><dd>{{ $commande['modele'] }}</dd></div>
        <div class="flex justify-between px-4 py-3"><dt class="text-sourdine">Couleur</dt><dd>{{ $commande['couleur'] }}</dd></div>
        <div class="flex justify-between px-4 py-3"><dt class="text-sourdine">Taille</dt><dd>{{ $commande['taille'] }}</dd></div>
        <div class="flex justify-between px-4 py-3"><dt class="text-sourdine">Quantité</dt><dd>{{ $commande['quantite'] }}</dd></div>
        <div class="flex justify-between px-4 py-3"><dt class="text-sourdine">Livraison</dt><dd>{{ $commande['quartier'] }}</dd></div>
    </dl>

    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <x-button href="https://wa.me/{{ $numero }}?text={{ urlencode($message) }}" target="_blank" rel="noopener">Écrire sur WhatsApp</x-button>
        <x-button href="{{ route('products.index') }}" variant="secondary">Retour à la collection</x-button>
    </div>
</div>
@endsection