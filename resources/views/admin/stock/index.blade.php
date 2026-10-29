@extends('layouts.app')

@section('title', 'Stock · Administration · TOP ÉLÉGANCE')

@section('content')
<div class="mx-auto max-w-6xl px-5 py-10">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="font-serif text-3xl font-extrabold">Stock</h1>

        <div class="flex gap-3">
            <a href="{{ route('admin.orders.index') }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Commandes</a>
            <a href="{{ route('admin.products.index') }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Produits</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Se déconnecter</button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <p class="mt-6 rounded border border-vert bg-vert/20 px-4 py-2 text-sm" role="status">{{ session('success') }}</p>
    @endif

    <p class="mt-6 text-sm text-sourdine">
        Un stock est « dormant » quand il reste des pièces mais qu'aucune commande n'a eu lieu depuis {{ $jours }} jours.
    </p>

    <div class="mt-4 overflow-x-auto rounded border border-ligne">
        <table class="w-full min-w-[640px] text-left text-sm">
            <thead class="bg-noir-doux text-sourdine">
                <tr>
                    <th class="px-4 py-3 font-medium">Modèle</th>
                    <th class="px-4 py-3 font-medium">Pièces en stock</th>
                    <th class="px-4 py-3 font-medium">Dernière commande</th>
                    <th class="px-4 py-3 font-medium">État</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ligne">
                @foreach ($lignes as $ligne)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $ligne['product']->name }}</td>
                        <td class="px-4 py-3">{{ $ligne['total'] }}</td>
                        <td class="px-4 py-3 text-sourdine">{{ $ligne['derniere']?->format('d/m/Y') ?? 'Aucune' }}</td>
                        <td class="px-4 py-3">
                            @if ($ligne['dormant'])
                                <span class="rounded-full border border-or px-3 py-0.5 text-or">Stock dormant</span>
                            @elseif ($ligne['total'] === 0)
                                <span class="text-sourdine">Aucun stock saisi</span>
                            @else
                                <span class="text-sourdine">Actif</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.stock.edit', $ligne['product']) }}" class="rounded border border-ligne px-3 py-1.5 text-sm hover:text-or">Modifier</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
