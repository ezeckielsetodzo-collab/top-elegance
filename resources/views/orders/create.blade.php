@extends('layouts.app')

@section('title', 'Précommander · TOP ÉLÉGANCE')

@section('content')
@php
    $modeleChoisi = old('modele', $choix['modele'] ?? ($ouverts[0]['slug'] ?? ''));
    $couleurChoisie = old('couleur', $choix['couleur'] ?? '');
    $champ = 'mt-1 w-full rounded border border-ligne bg-noir-doux px-4 py-3 text-ivoire placeholder:text-sourdine focus:border-or';
@endphp

<div class="mx-auto max-w-2xl px-5 py-12"
     x-data="{
        produits: @js($ouverts),
        modele: @js($modeleChoisi),
        couleur: @js($couleurChoisie),
        get couleurs() { const p = this.produits.find(x => x.slug === this.modele); return p ? p.colors : [] },
        choisir() { if (!this.couleurs.includes(this.couleur)) this.couleur = this.couleurs[0] ?? '' }
     }"
     x-init="choisir()">

    <h1 class="font-serif text-4xl font-extrabold">Précommander</h1>
    <p class="mt-3 text-sourdine">Remplissez le formulaire : nous vous confirmons le prix, le délai et le paiement par WhatsApp.</p>

    <form method="POST" action="{{ route('orders.store') }}" class="mt-8 space-y-6" novalidate>
        @csrf

        <div>
            <label for="nom" class="text-sm">Nom complet</label>
            <input id="nom" name="nom" type="text" value="{{ old('nom') }}" autocomplete="name" class="{{ $champ }}"
                   @error('nom') aria-invalid="true" @enderror>
            @error('nom') <p class="mt-1 text-sm text-[#e5877e]">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="telephone" class="text-sm">Téléphone (WhatsApp de préférence)</label>
            <input id="telephone" name="telephone" type="tel" inputmode="tel" value="{{ old('telephone') }}" autocomplete="tel"
                   placeholder="+228 90 00 00 00" class="{{ $champ }}" @error('telephone') aria-invalid="true" @enderror>
            @error('telephone') <p class="mt-1 text-sm text-[#e5877e]">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="quartier" class="text-sm">Quartier ou adresse de livraison</label>
            <input id="quartier" name="quartier" type="text" value="{{ old('quartier') }}" autocomplete="street-address"
                   class="{{ $champ }}" @error('quartier') aria-invalid="true" @enderror>
            @error('quartier') <p class="mt-1 text-sm text-[#e5877e]">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <label for="modele" class="text-sm">Modèle</label>
                <select id="modele" name="modele" x-model="modele" @change="choisir()" class="{{ $champ }}">
                    @foreach ($ouverts as $p)
                        <option value="{{ $p['slug'] }}">{{ $p['name'] }}</option>
                    @endforeach
                </select>
                @error('modele') <p class="mt-1 text-sm text-[#e5877e]">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="couleur" class="text-sm">Couleur</label>
                <select id="couleur" name="couleur" x-model="couleur" class="{{ $champ }}">
                    <template x-for="c in couleurs" :key="c">
                        <option :value="c" x-text="c"></option>
                    </template>
                </select>
                @error('couleur') <p class="mt-1 text-sm text-[#e5877e]">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div>
                <p class="text-sm">Taille</p>
                <div class="mt-1"><x-size-selector name="taille" selected="{{ old('taille', 'M') }}" /></div>
                @error('taille') <p class="mt-1 text-sm text-[#e5877e]">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="quantite" class="text-sm">Quantité</label>
                <input id="quantite" name="quantite" type="number" min="1" max="10" value="{{ old('quantite', 1) }}" class="{{ $champ }}">
                @error('quantite') <p class="mt-1 text-sm text-[#e5877e]">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Piège à robots : champ invisible, un humain ne le remplit jamais --}}
        <div class="hidden" aria-hidden="true">
            <label>Ne pas remplir <input type="text" name="site_web" tabindex="-1" autocomplete="off"></label>
        </div>

        <div class="space-y-3 text-sm">
            <label class="flex gap-3">
                <input type="checkbox" name="consentement" value="1" @checked(old('consentement')) class="mt-1 size-4 accent-[#d4a84b]">
                <span>J’accepte que TOP ÉLÉGANCE conserve mes coordonnées pour traiter ma commande et me contacter.</span>
            </label>
            @error('consentement') <p class="text-[#e5877e]">{{ $message }}</p> @enderror

            <label class="flex gap-3">
                <input type="checkbox" name="cgv" value="1" @checked(old('cgv')) class="mt-1 size-4 accent-[#d4a84b]">
                <span>J’ai lu et j’accepte les <a href="#" class="text-or underline">conditions de vente</a>.</span>
            </label>
            @error('cgv') <p class="text-[#e5877e]">{{ $message }}</p> @enderror
        </div>

        <x-button type="submit">Envoyer ma précommande</x-button>
    </form>
</div>
@endsection