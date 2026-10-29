@extends('layouts.app')

@section('title', 'Commandes · Administration · TOP ÉLÉGANCE')

@section('content')
<div class="mx-auto max-w-6xl px-5 py-10">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="font-serif text-3xl font-extrabold">Commandes</h1>

        <div class="flex gap-3">
            <a href="{{ route('admin.products.index') }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Produits</a>
            <a href="{{ route('admin.stock.index') }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Stock</a>
            <a href="{{ route('admin.ambassadors.index') }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Ambassadeurs</a>
            <a href="{{ route('admin.finances.index') }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Finances</a>
            <a href="{{ route('admin.indicators.index') }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Indicateurs</a>
            <a href="{{ route('admin.polls.index') }}" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">Sondage</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="rounded border border-ligne px-3 py-1.5 text-sm text-sourdine hover:text-ivoire">
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <p class="mt-6 rounded border border-vert bg-vert/20 px-4 py-2 text-sm" role="status">{{ session('success') }}</p>
    @endif

    <div class="mt-6 flex flex-wrap gap-2" role="group" aria-label="Filtrer par statut">
        <a href="{{ route('admin.orders.index') }}"
           class="rounded-full border px-4 py-1.5 text-sm {{ $status ? 'border-ligne text-sourdine hover:text-ivoire' : 'border-transparent bg-or text-noir' }}">
            Toutes ({{ $counts->sum() }})
        </a>
        @foreach (\App\Models\Order::STATUSES as $key => $label)
            <a href="{{ route('admin.orders.index', ['statut' => $key]) }}"
               class="rounded-full border px-4 py-1.5 text-sm {{ $status === $key ? 'border-transparent bg-or text-noir' : 'border-ligne text-sourdine hover:text-ivoire' }}">
                {{ $label }} ({{ $counts[$key] ?? 0 }})
            </a>
        @endforeach
    </div>

    <div class="mt-6 overflow-x-auto rounded border border-ligne">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="bg-noir-doux text-sourdine">
                <tr>
                    <th class="px-4 py-3 font-medium">N°</th>
                    <th class="px-4 py-3 font-medium">Date</th>
                    <th class="px-4 py-3 font-medium">Client</th>
                    <th class="px-4 py-3 font-medium">Articles</th>
                    <th class="px-4 py-3 font-medium">Total</th>
                    <th class="px-4 py-3 font-medium">Statut</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-t border-ligne align-top">
                        <td class="px-4 py-3">
                            <p>{{ $order->id }}</p>
                            <p class="mt-1 text-xs"><a href="{{ route('admin.documents.order', $order) }}" target="_blank" class="text-sourdine underline hover:text-ivoire">Bon</a> · <a href="{{ route('admin.documents.delivery', $order) }}" target="_blank" class="text-sourdine underline hover:text-ivoire">Livraison</a></p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <p>{{ $order->customer->name }}</p>
                            <p class="text-sourdine">{{ $order->customer->phone }}</p>
                            @if ($order->delivery_address)
                                <p class="text-sourdine">{{ $order->delivery_address }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @foreach ($order->items as $item)
                                <p>{{ $item->quantity }} × {{ $item->product->name }}</p>
                                <p class="text-sourdine">{{ $item->color->name }} · taille {{ $item->size->name }}</p>
                            @endforeach
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            {{ number_format($order->total, 0, ',', ' ') }} FCFA
                            @if ($order->ambassador)
                                <p class="text-sourdine">Code {{ $order->ambassador->code }}@if ($order->discount) · −{{ number_format($order->discount, 0, ',', ' ') }} @endif</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <label for="statut-{{ $order->id }}" class="sr-only">Statut de la commande {{ $order->id }}</label>
                                <select id="statut-{{ $order->id }}" name="status"
                                        class="rounded border border-ligne bg-noir-doux px-2 py-1 text-ivoire">
                                    @foreach (\App\Models\Order::STATUSES as $key => $label)
                                        <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="rounded border border-ligne px-2 py-1 text-sourdine hover:text-or">OK</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sourdine">Aucune commande pour le moment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
</div>
@endsection