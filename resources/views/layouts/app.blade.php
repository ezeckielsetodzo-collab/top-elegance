<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'TOP ÉLÉGANCE : streetwear élégant, né à Lomé, Togo.')">
    <title>@yield('title', 'TOP ÉLÉGANCE')</title>
    <link rel="canonical" href="{{ url()->current() }}">
    <meta name="theme-color" content="#0d0d0c">
    <meta property="og:site_name" content="TOP ÉLÉGANCE">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:title" content="@yield('title', 'TOP ÉLÉGANCE')">
    <meta property="og:description" content="@yield('description', 'TOP ÉLÉGANCE : streetwear élégant, né à Lomé, Togo.')">
    <meta property="og:url" content="{{ url()->current() }}">
    @if (file_exists(public_path('images/partage.png')))
        <meta property="og:image" content="{{ asset('images/partage.png') }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;800&family=Outfit:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">

    <a href="#contenu" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-30 focus:rounded focus:bg-or focus:px-4 focus:py-2 focus:text-noir">
        Aller au contenu
    </a>

    <header class="sticky top-0 z-20 border-b border-ligne bg-noir"
            x-data="{ open: false }" @keydown.escape.window="open = false">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-5">
            <a href="{{ url('/') }}" class="font-serif text-lg font-extrabold tracking-wide">TOP ÉLÉGANCE</a>

            <nav class="hidden gap-8 text-[15px] text-sourdine sm:flex" aria-label="Navigation principale">
                {{-- Remplacer les # par les vraies routes quand elles existeront --}}
                <a href="{{ route('products.index') }}" class="hover:text-or">Collection</a>
                <a href="{{ route('brand') }}" class="hover:text-or">La marque</a>
                <a href="{{ route('orders.create') }}" class="hover:text-or">Commander</a>
            </nav>

            <button type="button" class="rounded border border-ligne px-3 py-1.5 text-sm sm:hidden"
                    @click="open = !open" :aria-expanded="open" aria-controls="menu-mobile">
                <span x-text="open ? 'Fermer' : 'Menu'">Menu</span>
            </button>
        </div>

        <nav id="menu-mobile" x-cloak x-show="open" x-transition
             class="flex flex-col gap-4 border-t border-ligne px-5 py-4 text-sourdine sm:hidden"
             aria-label="Menu mobile">
            <a href="{{ route('products.index') }}" class="hover:text-or" @click="open = false">Collection</a>
            <a href="{{ route('brand') }}" class="hover:text-or" @click="open = false">La marque</a>
            <a href="{{ route('orders.create') }}" class="hover:text-or" @click="open = false">Commander</a>
        </nav>
    </header>

    <main id="contenu" class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-ligne bg-noir-doux">
        <div class="mx-auto grid max-w-6xl gap-8 px-5 py-10 sm:grid-cols-2">
            <div>
                <p class="font-serif text-xl font-extrabold">TOP ÉLÉGANCE</p>
                <p class="mt-2 text-sourdine">Style, confiance, élégance.</p>
            </div>
            <div class="flex