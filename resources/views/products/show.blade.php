@extends('layouts.app')

@section('title', $product['name'].' · TOP ÉLÉGANCE')
@section('description', $product['tagline'].'. '.$product['description'])

@section('content')
@php
    $swatches = ['Noir' => '#111111', 'Blanc' => '#f4f1ea', 'Gris' => '#8a8a88', 'Beige' => '#d9c4a0', 'Vert' => '#0f4a2c', 'Doré' => '#d4a84b'];
    $partage = urlencode($product['name'].' · TOP ÉLÉGANCE '.url()->current());
@endphp

<div class="mx-auto max-w-6xl px-5 py-10"
     x-data="{ vue: 0, vues: ['Face', 'Dos', 'Détail'], images: @js($product['images'] ?? []), couleur: @js($product['colors'][0] ?? '') }">

    <nav class="mb-6 text-sm text-sourdine" aria-label="Fil d’Ariane">
        <a href="{{ route('products.index') }}" class="hover:text-or">Collection</a>
        <span aria-hidden="true"> / </span>
        <span>{{ $product['name'] }}</span>
    </nav>

    <div class="grid gap-10 md:grid-cols-2">
        <div>
            <div class="relative grid aspect-square place-items-center rounded border border-ligne bg-noir-doux">
                <template x-if="images.length">
                    <img :src="images[vue]" :alt="@js($product['name']) + ' — ' + vues[vue]" class="h-full w-full rounded object-cover">
                </template>
                <template x-if="!images.length">
                    <svg viewBox="0 0 100 100" class="w-3/4" role="img" aria-label="Silhouette provisoire du modèle {{ $product['name'] }}">
                        <path fill="{{ $product['color'] }}" stroke="#8884" d="M35 10 L15 20 L5 40 L20 46 L26 38 L26 90 L74 90 L74 38 L80 46 L95 40 L85 20 L65 10 Q50 22 35 10 Z"/>
                    </svg>
                </template>
                <span class="absolute bottom-3 left-3 rounded-full border border-ligne bg-noir px-3 py-1 text-xs text-sourdine" x-text="vues[vue]"></span>
            </div>
            <div class="mt-3 grid grid-cols-3 gap-3">
                <template x-for="(v, n) in vues" :key="v">
                    <button type="button" @click="vue = n" :aria-pressed="vue === n"
                            class="rounded border py-2 text-sm transition"
                            :class="vue === n ? 'border-or text-or' : 'border-ligne text-sourdine hover:text-ivoire'"
                            x-text="v"></button>
                </template>
            </div>
        </div>

        <div>
            <div class="flex flex-wrap gap-2">
                <x-badge :type="$product['preorder_open'] ? 'preorder' : 'soon'" />
                @if ($product['is_limited'])
                    <x-badge type="limited" />
                @endif
            </div>

            <h1 class="mt-4 font-serif text-4xl font-extrabold">{{ $product['name'] }}</h1>
            <p class="mt-1 text-or">{{ $product['tagline'] }}</p>
            <p class="mt-5 text-sourdine">{{ $product['description'] }}</p>
            @if ($product['is_limited'])
                <p class="mt-3 text-sm text-or">Série limitée : petites quantités, pièces numérotées.</p>
            @endif

            <p class="mb-2 mt-8 text-sm">Couleur : <span class="text-or" x-text="couleur"></span></p>
            <div class="flex gap-3" role="group" aria-label="Choix de la couleur">
                @foreach ($product['colors'] as $c)
                    <button type="button" @click="couleur = @js($c)" :aria-pressed="couleur === @js($c)"
                            aria-label="{{ $c }}" title="{{ $c }}"
                            class="size-9 rounded-full border-2 transition"
                            :class="couleur === @js($c) ? 'border-or' : 'border-ligne'"
                            style="background-color: {{ $swatches[$c] ?? '#555555' }}"></button>
                @endforeach
            </div>

            <p class="mb-2 mt-6 text-sm">Taille</p>
            <x-size-selector />

            <p class="mt-6 text-sm text-sourdine">Prix annoncé à l’ouverture des précommandes.</p>

            <div class="mt-6">
                @if ($product['preorder_open'])
                  <x-button x-on:click="window.location.href = '{{ route('orders.create') }}?modele={{ $product['slug'] }}&couleur=' + encodeURIComponent(couleur)">Précommander</x-button>
                @else
                    <x-button disabled>Bientôt disponible</x-button>
                @endif
            </div>

            <div class="mt-8 flex flex-wrap gap-6 border-t border-ligne pt-6 text-sm">
                <a href="https://wa.me/?text={{ $partage }}" target="_blank" rel="noopener" class="text-sourdine hover:text-or">Partager sur WhatsApp</a>
                <button type="button" x-data="{ ok: false }" class="text-sourdine hover:text-or"
                        @click="navigator.clipboard.writeText(window.location.href).then(() => { ok = true; setTimeout(() => ok = false, 2000) })"
                        x-text="ok ? 'Lien copié' : 'Copier le lien'">Copier le lien</button>
            </div>
        </div>
    </div>
</div>
@endsection