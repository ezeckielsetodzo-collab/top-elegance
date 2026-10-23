@extends('layouts.app')

@section('title', 'TOP ÉLÉGANCE · Savoir s’habiller n’est pas un crime')
@section('description', 'TOP ÉLÉGANCE : streetwear élégant né à Lomé. Découvrez le DROP 01, dix modèles, une seule vision.')

@section('content')
@php
    // Date d'ouverture du drop : provisoire, le Dev 2 fournira la vraie valeur.
    // Lomé est à l'heure GMT (UTC+0), donc le Z final est correct.
    $dropDate = config('shop.drop_date', '2026-12-01T18:00:00Z');
@endphp

<section class="mx-auto flex max-w-6xl flex-col items-center px-5 py-16 text-center md:py-24">
    <svg viewBox="0 0 60 36" class="w-14 text-or" fill="currentColor" aria-hidden="true">
        <path d="M4 32 L2 8 L16 20 L30 2 L44 20 L58 8 L56 32 Z"/>
    </svg>
    <p class="mb-6 bg-linear-to-br from-[#f1d27a] via-or to-[#b9862a] bg-clip-text font-serif text-[110px] font-extrabold leading-none tracking-tighter text-transparent md:text-[170px]"
       aria-hidden="true">TE</p>

    <h1 class="font-serif text-3xl font-extrabold md:text-5xl">Savoir s’habiller n’est pas un crime.</h1>
    <p class="mt-4 max-w-xl text-lg text-sourdine">
        Streetwear élégant, né à Lomé. DROP 01 : dix modèles, une seule vision.
    </p>

    <div class="mt-10 w-full max-w-lg rounded border border-ligne bg-noir-doux p-6"
         x-data="{
            target: new Date(@js($dropDate)).getTime(),
            now: Date.now(),
            init() { setInterval(() => this.now = Date.now(), 1000) },
            get diff() { return Math.max(0, this.target - this.now) },
            get over() { return this.target - this.now <= 0 },
            get d() { return Math.floor(this.diff / 86400000) },
            get h() { return Math.floor(this.diff % 86400000 / 3600000) },
            get m() { return Math.floor(this.diff % 3600000 / 60000) },
            get s() { return Math.floor(this.diff % 60000 / 1000) },
            pad(n) { return String(n).padStart(2, '0') }
         }">
        <p class="mb-4 text-sm uppercase tracking-widest text-or" x-text="over ? 'Le drop est ouvert' : 'Ouverture du DROP 01 dans'">
            Ouverture du DROP 01 dans
        </p>
        <div x-show="!over" class="grid grid-cols-4 gap-2 lining-nums tabular-nums" role="timer" aria-live="off">
            <div><p class="font-serif text-4xl font-extrabold" x-text="pad(d)">00</p><p class="text-xs text-sourdine">jours</p></div>
            <div><p class="font-serif text-4xl font-extrabold" x-text="pad(h)">00</p><p class="text-xs text-sourdine">heures</p></div>
            <div><p class="font-serif text-4xl font-extrabold" x-text="pad(m)">00</p><p class="text-xs text-sourdine">minutes</p></div>
            <div><p class="font-serif text-4xl font-extrabold" x-text="pad(s)">00</p><p class="text-xs text-sourdine">secondes</p></div>
        </div>
        <p x-cloak x-show="over" class="text-lg">Les précommandes sont ouvertes. Choisissez votre modèle.</p>
    </div>

    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <x-button href="#">Voir la collection</x-button>
        <x-button href="#" variant="secondary">Précommander</x-button>
    </div>
</section>

<section class="border-t border-ligne">
    <div class="mx-auto grid max-w-6xl gap-8 px-5 py-14 text-center sm:grid-cols-3 sm:text-left">
        <div>
            <h2 class="font-serif text-xl font-semibold">Qualité premium</h2>
            <p class="mt-2 text-sourdine">Coton confortable, finitions soignées, étiquette TOP ÉLÉGANCE.</p>
        </div>
        <div>
            <h2 class="font-serif text-xl font-semibold">Éditions limitées</h2>
            <p class="mt-2 text-sourdine">De petites séries, certaines numérotées, pour rester rares.</p>
        </div>
        <div>
            <h2 class="font-serif text-xl font-semibold">Fierté togolaise</h2>
            <p class="mt-2 text-sourdine">Lomé, 228, des couleurs et des motifs d’ici, avec sobriété.</p>
        </div>
    </div>
</section>
@endsection