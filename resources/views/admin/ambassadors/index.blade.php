@extends('layouts.app')

@section('title', 'Ambassadeurs · Administration · TOP ÉLÉGANCE')

@section('content')
<div class="mx-auto max-w-6xl px-5 py-10">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="font-serif text-3xl font-extrabold">Ambassadeurs</h1>

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

    <h2 class="mt-8 font-serif text-xl font-extrabold">Nouvel ambassadeur</h2>
    <form method="POST" action="{{ route('admin.ambassadors.store') }}" class="mt-3 grid gap-3 rounded border border-ligne p-4 sm:grid-cols-6">
        @csrf
        <input name="name" value="{{ old('name') }}" placeholder="Nom" required aria-label="Nom" class="rounded border border-ligne bg-noir-doux px-2 py-1.5 text-ivoire sm:col-span-2">
        <input name="phone" value="{{ old('phone') }}" placeholder="Téléphone" aria-label="Téléphone" class="rounded border border-ligne bg-noir-doux px-2 py-1.5 text-ivoire">
        <input name="code" value="{{ old('code') }}" placeholder="Code (ex. JEAN10)" required aria-label="Code promo" class="rounded border border-ligne bg-noir-doux px-2 py-1.5 uppercase text-ivoire">
        <input name="discount_percent" type="number" min="0" max="100" value="{{ old('discount_percent', 0) }}" aria-label="Réduction client en %" title="Réduction client (%)" class="rounded border border-ligne bg-noir-doux px-2 py-1.5 text-ivoire">
        <input name="commission_percent" type="number" min="0" max="100" value="{{ old('commission_percent', 10) }}" aria-label="Commission en %" title="Commission (%)" class="rounded border border-ligne bg-noir-doux px-2 py-1.5 text-ivoire">
        <button type="submit" class="rounded bg-or px-4 py-2 font-medium text-noir hover:opacity-90 sm:col-span-6 sm:justify-self-start">Ajouter</button>
        <p class="text-sm text-sourdine sm:col-span-6">Les deux derniers champs : réduction offerte au client (%) puis commission de l'ambassadeur (%).</p>
    </form>

    <div class="mt-10 flex flex-wrap items-center justify-between gap-3">
        <h2 class="font-serif text-xl font-extrabold">Ventes de {{ $titreMois }}</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.ambassadors.index', ['mois' => $moisPrecedent]) }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">← Mois précédent</a>
            <a href="{{ route('admin.ambassadors.index', ['mois' => $moisSuivant]) }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Mois suivant →</a>
        </div>
    </div>

    <div class="mt-4 overflow-x-auto rounded border border-ligne">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="bg-noir-doux text-sourdine">
                <tr>
                    <th class="px-4 py-3 font-medium">Ambassadeur</th>
                    <th class="px-4 py-3 font-medium">Code</th>
                    <th class="px-4 py-3 font-medium">Commandes</th>
                    <th class="px-4 py-3 font-medium">Ventes</th>
                    <th class="px-4 py-3 font-medium">Commission à payer</th>
                    <th class="px-4 py-3 font-medium">Réduction %</th>
                    <th class="px-4 py-3 font-medium">Commission %</th>
                    <th class="px-4 py-3 font-medium">Actif</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ligne">
                @forelse ($lignes as $ligne)
                    @php($a = $ligne['ambassador'])
                    <tr>
                        <td class="px-4 py-3"><p class="font-medium">{{ $a->name }}</p><p class="text-sourdine">{{ $a->phone }}</p></td>
                        <td class="px-4 py-3 font-medium">{{ $a->code }}</td>
                        <td class="px-4 py-3">{{ $ligne['commandes'] }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ number_format($ligne['ventes'], 0, ',', ' ') }} FCFA</td>
                        <td class="px-4 py-3 whitespace-nowrap font-medium text-or">{{ number_format($ligne['commission'], 0, ',', ' ') }} FCFA</td>
                        <td class="px-4 py-3"><input form="amb-{{ $a->id }}" name="discount_percent" type="number" min="0" max="100" value="{{ $a->discount_percent }}" aria-label="Réduction de {{ $a->name }}" class="w-16 rounded border border-ligne bg-noir-doux px-2 py-1 text-ivoire"></td>
                        <td class="px-4 py-3"><input form="amb-{{ $a->id }}" name="commission_percent" type="number" min="0" max="100" value="{{ $a->commission_percent }}" aria-label="Commission de {{ $a->name }}" class="w-16 rounded border border-ligne bg-noir-doux px-2 py-1 text-ivoire"></td>
                        <td class="px-4 py-3"><input form="amb-{{ $a->id }}" name="active" type="checkbox" value="1" @checked($a->active) aria-label="Actif"></td>
                        <td class="px-4 py-3 text-right">
                            <form id="amb-{{ $a->id }}" method="POST" action="{{ route('admin.ambassadors.update', $a) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="rounded bg-or px-3 py-1.5 text-sm font-medium text-noir hover:opacity-90">Enregistrer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="px-4 py-6 text-center text-sourdine">Aucun ambassadeur pour le moment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
