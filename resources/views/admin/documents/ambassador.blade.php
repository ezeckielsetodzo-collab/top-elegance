@extends('admin.documents.base')

@section('title', 'Fiche ambassadeur '.$ambassador->name)
@section('document', 'Fiche ambassadeur')
@section('reference')
    <p>{{ ucfirst($titreMois) }}</p>
@endsection

@section('contenu')
@php($fcfa = fn ($n) => number_format($n, 0, ',', ' ').' FCFA')
<div class="bloc">
    <strong>{{ $ambassador->name }}</strong><br>
    @if ($ambassador->phone)Téléphone : {{ $ambassador->phone }}<br>@endif
    Code promo : <strong>{{ $ambassador->code }}</strong><br>
    Réduction offerte aux clients : {{ $ambassador->discount_percent }} %<br>
    Commission : {{ $ambassador->commission_percent }} %
</div>

<h2>Ventes attribuées</h2>
<table>
    <thead>
        <tr><th>N°</th><th>Date</th><th>Client</th><th class="droite">Montant</th></tr>
    </thead>
    <tbody>
        @forelse ($commandes as $commande)
            <tr>
                <td>{{ $commande->id }}</td>
                <td>{{ $commande->created_at->format('d/m/Y') }}</td>
                <td>{{ $commande->customer->name }}</td>
                <td class="droite">{{ $fcfa($commande->total) }}</td>
            </tr>
        @empty
            <tr><td colspan="4">Aucune vente ce mois-ci.</td></tr>
        @endforelse
        <tr class="total"><td colspan="3" class="droite">Total des ventes</td><td class="droite">{{ $fcfa($ventes) }}</td></tr>
        <tr class="total"><td colspan="3" class="droite">Commission à payer ({{ $ambassador->commission_percent }} %)</td><td class="droite">{{ $fcfa($commission) }}</td></tr>
    </tbody>
</table>

<div class="signatures">
    <div>Pour TOP ÉLÉGANCE</div>
    <div>L'ambassadeur : reçu le ____ / ____ / ______</div>
</div>
@endsection
