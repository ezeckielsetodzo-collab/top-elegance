@extends('layouts.app')

@section('title', 'Produits · Administration · TOP ÉLÉGANCE')

@section('content')
<div class="mx-auto max-w-6xl px-5 py-10">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="font-serif text-3xl font-extrabold">Produits</h1>

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

    <div class="mt-6 overflow-x-auto rounded border border-ligne">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="bg-noir-doux text-sourdine">
                <tr>
                    <th class="px-4 py-3 font-medium">Modèle</th>
                    <th class="px-4 py-3 font-medium">Prix (FCFA)</th>
                    <th class="px-4 py-3 font-medium">Coût (FCFA)</th>
                    <th class="px-4 py-3 font-medium">Précommande ouverte</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ligne">
                @foreach ($products as $product)
                    <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium">{{ $product->name }}</p>
                                <p class="text-sourdine">{{ $product->category }}@if ($product->is_limited) · édition limitée @endif</p>
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" form="produit-{{ $product->id }}" name="price" min="0" step="1" value="{{ $product->price }}"
                                       aria-label="Prix de {{ $product->name }}"
                                       class="w-28 rounded border border-ligne bg-noir-doux px-2 py-1 text-ivoire">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" form="produit-{{ $product->id }}" name="cost" min="0" step="1" value="{{ $product->cost }}"
                                       aria-label="Coût de {{ $product->name }}"
                                       class="w-28 rounded border border-ligne bg-noir-doux px-2 py-1 text-ivoire">
                            </td>
                            <td class="px-4 py-3">
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" form="produit-{{ $product->id }}" name="preorder_open" value="1" @checked($product->preorder_open)>
                                    <span>{{ $product->preorder_open ? 'Ouverte' : 'Fermée' }}</span>
                                </label>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form id="produit-{{ $product->id }}" method="POST" action="{{ route('admin.products.update', $product) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="rounded bg-or px-3 py-1.5 text-sm font-medium text-noir hover:opacity-90">Enregistrer</button>
                                </form>
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
