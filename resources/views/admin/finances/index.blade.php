@extends('layouts.app')

@section('title', 'Finances · Administration · TOP ÉLÉGANCE')

@section('content')
@php($fcfa = fn ($n) => number_format($n, 0, ',', ' ').' FCFA')
<div class="mx-auto max-w-6xl px-5 py-10">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="font-serif text-3xl font-extrabold">Finances</h1>

        <div class="flex gap-3">
            <a href="{{ route('admin.orders.index') }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Commandes</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Se déconnecter</button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <p class="mt-6 rounded border border-vert bg-vert/20 px-4 py-2 text-sm" role="status">{{ session('success') }}</p>
    @endif
    @if ($errors->any())
        <p class="mt-6 rounded border border-red-400 px-4 py-2 text-sm text-red-400" role="alert">{{ $errors->first() }}</p>
    @endif

    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded border border-ligne p-4"><p class="text-sm text-sourdine">Entrées</p><p class="mt-1 text-xl font-medium">{{ $fcfa($entrees) }}</p></div>
        <div class="rounded border border-ligne p-4"><p class="text-sm text-sourdine">Sorties</p><p class="mt-1 text-xl font-medium">{{ $fcfa($sorties) }}</p></div>
        <div class="rounded border border-ligne p-4"><p class="text-sm text-sourdine">Solde</p><p class="mt-1 text-xl font-medium {{ $solde < 0 ? 'text-red-400' : 'text-or' }}">{{ $fcfa($solde) }}</p></div>
    </div>

    <h2 class="mt-10 font-serif text-xl font-extrabold">Nouvelle écriture</h2>
    <form method="POST" action="{{ route('admin.finances.store') }}" class="mt-3 grid gap-3 rounded border border-ligne p-4 sm:grid-cols-5">
        @csrf
        <input type="date" name="entry_date" value="{{ old('entry_date', now()->format('Y-m-d')) }}" required aria-label="Date" class="rounded border border-ligne bg-noir-doux px-2 py-1.5 text-ivoire">
        <input name="label" value="{{ old('label') }}" placeholder="Motif (ex. achat de tissu)" required aria-label="Motif" class="rounded border border-ligne bg-noir-doux px-2 py-1.5 text-ivoire sm:col-span-2">
        <select name="type" required aria-label="Type" class="rounded border border-ligne bg-noir-doux px-2 py-1.5 text-ivoire">
            <option value="entree" @selected(old('type') === 'entree')>Entrée</option>
            <option value="sortie" @selected(old('type') === 'sortie')>Sortie</option>
        </select>
        <input type="number" name="amount" min="1" step="1" value="{{ old('amount') }}" placeholder="Montant FCFA" required aria-label="Montant en FCFA" class="rounded border border-ligne bg-noir-doux px-2 py-1.5 text-ivoire">
        <button type="submit" class="rounded bg-or px-4 py-2 font-medium text-noir hover:opacity-90 sm:col-span-5 sm:justify-self-start">Ajouter</button>
    </form>

    <h2 class="mt-10 font-serif text-xl font-extrabold">Registre</h2>
    <div class="mt-3 overflow-x-auto rounded border border-ligne">
        <table class="w-full min-w-[700px] text-left text-sm">
            <thead class="bg-noir-doux text-sourdine">
                <tr>
                    <th class="px-4 py-3 font-medium">Date</th>
                    <th class="px-4 py-3 font-medium">Motif</th>
                    <th class="px-4 py-3 font-medium">Entrée</th>
                    <th class="px-4 py-3 font-medium">Sortie</th>
                    <th class="px-4 py-3 font-medium">Solde</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ligne">
                @forelse ($lignes as $ligne)
                    @php($e = $ligne['entry'])
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $e->entry_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $e->label }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $e->income ? $fcfa($e->income) : '' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $e->expense ? $fcfa($e->expense) : '' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap font-medium {{ $ligne['solde'] < 0 ? 'text-red-400' : '' }}">{{ $fcfa($ligne['solde']) }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.finances.destroy', $e) }}" onsubmit="return confirm('Supprimer cette écriture ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded border border-ligne px-3 py-1 text-sm text-sourdine hover:text-ivoire">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-sourdine">Aucune écriture pour le moment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <h2 class="mt-10 font-serif text-xl font-extrabold">Marge par produit</h2>
    <p class="mt-1 text-sm text-sourdine">Calculée avec le prix et le coût saisis dans « Produits », sur les commandes non annulées.</p>
    <div class="mt-3 overflow-x-auto rounded border border-ligne">
        <table class="w-full min-w-[700px] text-left text-sm">
            <thead class="bg-noir-doux text-sourdine">
                <tr>
                    <th class="px-4 py-3 font-medium">Modèle</th>
                    <th class="px-4 py-3 font-medium">Marge unitaire</th>
                    <th class="px-4 py-3 font-medium">Taux</th>
                    <th class="px-4 py-3 font-medium">Pièces commandées</th>
                    <th class="px-4 py-3 font-medium">Marge totale</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ligne">
                @foreach ($marges as $ligne)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $ligne['product']->name }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $ligne['unite'] !== null ? $fcfa($ligne['unite']) : 'Prix ou coût manquant' }}</td>
                        <td class="px-4 py-3">{{ $ligne['taux'] !== null ? $ligne['taux'].' %' : '—' }}</td>
                        <td class="px-4 py-3">{{ $ligne['quantite'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $ligne['total'] !== null ? $fcfa($ligne['total']) : '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
