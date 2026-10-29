@extends('layouts.app')

@section('title', 'Indicateurs · Administration · TOP ÉLÉGANCE')

@section('content')
@php($fcfa = fn ($n) => number_format($n, 0, ',', ' ').' FCFA')
<div class="mx-auto max-w-6xl px-5 py-10">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="font-serif text-3xl font-extrabold">Indicateurs</h1>

        <div class="flex gap-3">
            <a href="{{ route('admin.orders.index') }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Commandes</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Se déconnecter</button>
            </form>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="font-serif text-xl font-extrabold">{{ ucfirst($titreMois) }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.indicators.index', ['mois' => $moisPrecedent]) }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">← Mois précédent</a>
            <a href="{{ route('admin.indicators.index', ['mois' => $moisSuivant]) }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Mois suivant →</a>
        </div>
    </div>

    <p class="mt-2 text-sm text-sourdine">Les commandes annulées ne sont pas comptées.</p>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded border border-ligne p-4"><p class="text-sm text-sourdine">Chiffre d'affaires</p><p class="mt-1 text-xl font-medium text-or">{{ $fcfa($i['ca']) }}</p></div>
        <div class="rounded border border-ligne p-4">
            <p class="text-sm text-sourdine">Marge</p>
            <p class="mt-1 text-xl font-medium">{{ $fcfa($i['marge']) }}</p>
            @if ($i['couts_manquants'])<p class="mt-1 text-xs text-sourdine">Coût manquant sur certains modèles : marge surestimée.</p>@endif
        </div>
        <div class="rounded border border-ligne p-4"><p class="text-sm text-sourdine">Commandes</p><p class="mt-1 text-xl font-medium">{{ $i['commandes'] }}</p></div>
        <div class="rounded border border-ligne p-4"><p class="text-sm text-sourdine">Panier moyen</p><p class="mt-1 text-xl font-medium">{{ $fcfa($i['panier_moyen']) }}</p></div>
        <div class="rounded border border-ligne p-4"><p class="text-sm text-sourdine">Clients du mois</p><p class="mt-1 text-xl font-medium">{{ $i['clients'] }}</p><p class="mt-1 text-xs text-sourdine">dont {{ $i['nouveaux_clients'] }} nouveaux</p></div>
        <div class="rounded border border-ligne p-4"><p class="text-sm text-sourdine">Taux de réachat</p><p class="mt-1 text-xl font-medium">{{ $i['taux_reachat'] !== null ? $i['taux_reachat'].' %' : '—' }}</p><p class="mt-1 text-xs text-sourdine">clients ayant déjà commandé avant</p></div>
        <div class="rounded border border-ligne p-4">
            <p class="text-sm text-sourdine">Coût d'acquisition</p>
            <p class="mt-1 text-xl font-medium">{{ $i['cout_acquisition'] !== null ? $fcfa($i['cout_acquisition']) : '—' }}</p>
            <p class="mt-1 text-xs text-sourdine">commissions + réductions par nouveau client</p>
        </div>
        <div class="rounded border border-ligne p-4">
            <p class="text-sm text-sourdine">Produit le plus vendu</p>
            <p class="mt-1 text-xl font-medium">{{ $i['meilleur_produit']['nom'] ?? '—' }}</p>
            @if ($i['meilleur_produit'])<p class="mt-1 text-xs text-sourdine">{{ $i['meilleur_produit']['quantite'] }} pièces</p>@endif
        </div>
    </div>

    <div class="mt-4 rounded border border-ligne p-4">
        <p class="text-sm text-sourdine">Stock dormant</p>
        <p class="mt-1 text-xl font-medium {{ $i['stocks_dormants'] ? 'text-or' : '' }}">{{ $i['stocks_dormants'] }} {{ $i['stocks_dormants'] > 1 ? 'modèles' : 'modèle' }}</p>
        <p class="mt-1 text-xs"><a href="{{ route('admin.stock.index') }}" class="text-sourdine underline hover:text-ivoire">Voir le stock</a></p>
    </div>

    <h2 class="mt-10 font-serif text-xl font-extrabold">Ventes par ambassadeur</h2>
    <div class="mt-3 overflow-x-auto rounded border border-ligne">
        <table class="w-full min-w-[560px] text-left text-sm">
            <thead class="bg-noir-doux text-sourdine">
                <tr>
                    <th class="px-4 py-3 font-medium">Ambassadeur</th>
                    <th class="px-4 py-3 font-medium">Code</th>
                    <th class="px-4 py-3 font-medium">Commandes</th>
                    <th class="px-4 py-3 font-medium">Ventes</th>
                    <th class="px-4 py-3 font-medium">Commission</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ligne">
                @forelse ($i['ambassadeurs'] as $a)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $a['nom'] }}</td>
                        <td class="px-4 py-3">{{ $a['code'] }}</td>
                        <td class="px-4 py-3">{{ $a['commandes'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $fcfa($a['ventes']) }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $fcfa($a['commission']) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-sourdine">Aucune vente par ambassadeur ce mois-ci.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
