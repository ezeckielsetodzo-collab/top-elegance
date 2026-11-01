@extends('layouts.app')

@section('title', 'Clients · Administration · TOP ÉLÉGANCE')

@section('content')
<div class="mx-auto max-w-6xl px-5 py-10">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="font-serif text-3xl font-extrabold">Clients</h1>

        <div class="flex gap-3">
            <a href="{{ route('admin.orders.index') }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Commandes</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Se déconnecter</button>
            </form>
        </div>
    </div>

    <p class="mt-4 text-sm text-sourdine">
        Un achat = une commande payée ou livrée. Un client devient « fidèle » à partir de {{ $seuil }} {{ $seuil > 1 ? 'achats' : 'achat' }}.
    </p>

    <div class="mt-4 flex gap-2" role="group" aria-label="Filtrer les clients">
        <a href="{{ route('admin.customers.index') }}"
           class="rounded-full border px-4 py-1.5 text-sm {{ $fideles ? 'border-ligne text-sourdine hover:text-ivoire' : 'border-transparent bg-or text-noir' }}">Tous</a>
        <a href="{{ route('admin.customers.index', ['filtre' => 'fideles']) }}"
           class="rounded-full border px-4 py-1.5 text-sm {{ $fideles ? 'border-transparent bg-or text-noir' : 'border-ligne text-sourdine hover:text-ivoire' }}">Clients fidèles</a>
    </div>

    <div class="mt-6 overflow-x-auto rounded border border-ligne">
        <table class="w-full min-w-[820px] text-left text-sm">
            <thead class="bg-noir-doux text-sourdine">
                <tr>
                    <th class="px-4 py-3 font-medium">Client</th>
                    <th class="px-4 py-3 font-medium">Commandes</th>
                    <th class="px-4 py-3 font-medium">Achats</th>
                    <th class="px-4 py-3 font-medium">Dépensé</th>
                    <th class="px-4 py-3 font-medium">Code de parrainage</th>
                    <th class="px-4 py-3 font-medium">Filleuls</th>
                    <th class="px-4 py-3 font-medium">Parrainé par</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ligne">
                @forelse ($clients as $client)
                    <tr>
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $client->name }}
                                @if ($client->achats_count >= $seuil)
                                    <span class="ml-2 rounded-full border border-or px-2 py-0.5 text-xs text-or">Fidèle</span>
                                @endif
                            </p>
                            <p class="text-sourdine">{{ $client->phone }}</p>
                        </td>
                        <td class="px-4 py-3">{{ $client->commandes_count }}</td>
                        <td class="px-4 py-3">{{ $client->achats_count }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ number_format((int) $client->depense, 0, ',', ' ') }} FCFA</td>
                        <td class="px-4 py-3 font-medium">{{ $client->referral_code }}</td>
                        <td class="px-4 py-3">{{ $client->filleuls_count }}</td>
                        <td class="px-4 py-3 text-sourdine">{{ $client->referrer?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-sourdine">Aucun client.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $clients->links() }}</div>
</div>
@endsection
