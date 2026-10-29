@extends('admin.documents.base')

@section('title', 'Fiche de livraison n°'.$order->id)
@section('document', 'Fiche de livraison')
@section('reference')
    <p>Commande n° {{ $order->id }}</p>
    <p>Du {{ $order->created_at->format('d/m/Y') }}</p>
@endsection

@section('contenu')
@php($fcfa = fn ($n) => number_format($n, 0, ',', ' ').' FCFA')
<div class="bloc">
    <strong>À livrer à</strong><br>
    {{ $order->customer->name }}<br>
    Téléphone : {{ $order->customer->phone }}<br>
    Adresse : {{ $order->delivery_address ?: 'À confirmer avec le client' }}
</div>

<h2>Colis</h2>
<table>
    <thead>
        <tr><th>Modèle</th><th>Couleur</th><th>Taille</th><th class="droite">Qté</th><th>Contrôlé</th></tr>
    </thead>
    <tbody>
        @foreach ($order->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->color->name }}</td>
                <td>{{ $item->size->name }}</td>
                <td class="droite">{{ $item->quantity }}</td>
                <td>☐</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="bloc">
    <strong>Montant à encaisser à la livraison :</strong>
    {{ in_array($order->status, ['payee', 'livree'], true) ? 'Déjà payé' : $fcfa($order->total) }}
</div>

<div class="signatures">
    <div>Livreur (nom et signature)</div>
    <div>Client : reçu le ____ / ____ / ______</div>
</div>
@endsection
