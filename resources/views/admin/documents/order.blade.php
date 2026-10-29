@extends('admin.documents.base')

@section('title', 'Bon de commande n°'.$order->id)
@section('document', 'Bon de commande')
@section('reference')
    <p>N° {{ $order->id }}</p>
    <p>Du {{ $order->created_at->format('d/m/Y') }}</p>
@endsection

@section('contenu')
@php($fcfa = fn ($n) => number_format($n, 0, ',', ' ').' FCFA')
<div class="bloc">
    <strong>Client</strong><br>
    {{ $order->customer->name }}<br>
    Téléphone : {{ $order->customer->phone }}<br>
    @if ($order->delivery_address)Adresse de livraison : {{ $order->delivery_address }}<br>@endif
    Statut : {{ \App\Models\Order::STATUSES[$order->status] ?? $order->status }}
</div>

<h2>Articles</h2>
<table>
    <thead>
        <tr><th>Modèle</th><th>Couleur</th><th>Taille</th><th class="droite">Qté</th><th class="droite">Prix unitaire</th><th class="droite">Montant</th></tr>
    </thead>
    <tbody>
        @foreach ($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->color->name }}</td>
                <td>{{ $item->size->name }}</td>
                <td class="droite">{{ $item->quantity }}</td>
                <td class="droite">{{ $fcfa($item->unit_price) }}</td>
                <td class="droite">{{ $fcfa($item->unit_price * $item->quantity) }}</td>
            </tr>
        @endforeach
        @if ($order->discount)
            <tr>
                <td colspan="5" class="droite">Réduction @if ($order->ambassador)(code {{ $order->ambassador->code }})@endif</td>
                <td class="droite">− {{ $fcfa($order->discount) }}</td>
            </tr>
        @endif
        <tr class="total"><td colspan="5" class="droite">Total à payer</td><td class="droite">{{ $fcfa($order->total) }}</td></tr>
    </tbody>
</table>

<div class="signatures">
    <div>Signature de la marque</div>
    <div>Signature du client</div>
</div>
@endsection
