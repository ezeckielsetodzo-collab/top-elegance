@extends('layouts.app')

@section('title', 'Connexion administrateur · TOP ÉLÉGANCE')

@section('content')
<div class="mx-auto max-w-md px-5 py-16">
    <h1 class="font-serif text-3xl font-extrabold">Espace administrateur</h1>
    <p class="mt-2 text-sourdine">Réservé à l’équipe TOP ÉLÉGANCE.</p>

    <form method="POST" action="{{ route('admin.login.store') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-1 block text-sm text-sourdine">Adresse email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                   autocomplete="username"
                   class="w-full rounded border border-ligne bg-noir-doux px-3 py-2 text-ivoire focus:border-or">
            @error('email')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-1 block text-sm text-sourdine">Mot de passe</label>
            <input id="password" name="password" type="password" required autocomplete="current-password"
                   class="w-full rounded border border-ligne bg-noir-doux px-3 py-2 text-ivoire focus:border-or">
            @error('password')
                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full rounded bg-or px-4 py-2.5 font-medium text-noir hover:opacity-90">
            Se connecter
        </button>
    </form>
</div>
@endsection