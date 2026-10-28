@extends('layouts.app')

@section('title', 'Conditions de vente · TOP ÉLÉGANCE')

@section('content')
<div class="mx-auto max-w-3xl px-5 py-16">
    @unless (app()->isProduction())
        <p class="mb-8 rounded border border-or px-4 py-3 text-sm text-or">
            Texte provisoire (visible seulement hors production) : à compléter par le propriétaire et à faire relire par un professionnel du droit avant publication.
        </p>
    @endunless

    <h1 class="font-serif text-4xl font-extrabold">Conditions de vente</h1>

    <div class="mt-8 space-y-8 text-sourdine">
        <section>
            <h2 class="font-serif text-xl font-semibold text-ivoire">1. Commande</h2>
            <p class="mt-2">La commande précise le produit, la taille, la couleur, la quantité, le lieu de livraison et le contact du client. Une précommande est confirmée par TOP ÉLÉGANCE par WhatsApp, avec le prix et le délai.</p>
        </section>
        <section>
            <h2 class="font-serif text-xl font-semibold text-ivoire">2. Paiement</h2>
            <p class="mt-2">Modes de paiement acceptés : [à compléter]. Une commande personnalisée peut nécessiter un acompte de [à compléter] % avant le lancement de la production.</p>
        </section>
        <section>
            <h2 class="font-serif text-xl font-semibold text-ivoire">3. Délais</h2>
            <p class="mt-2">Délai indicatif : [à compléter] jours. Les délais peuvent varier selon la disponibilité et la production.</p>
        </section>
        <section>
            <h2 class="font-serif text-xl font-semibold text-ivoire">4. Échanges</h2>
            <p class="mt-2">Les échanges de taille ou de produit sont possibles selon la politique suivante : [à compléter]. Les produits personnalisés peuvent être soumis à des conditions particulières.</p>
        </section>
        <section>
            <h2 class="font-serif text-xl font-semibold text-ivoire">5. Livraison</h2>
            <p class="mt-2">Frais de livraison : [à compléter] F CFA ou selon la distance. Le client confirme son adresse et son numéro de téléphone avant l’expédition.</p>
        </section>
        <section>
            <h2 class="font-serif text-xl font-semibold text-ivoire">6. Réclamations</h2>
            <p class="mt-2">Toute réclamation doit être signalée rapidement, avec si possible des photos du produit et la référence de la commande, par WhatsApp : [à compléter].</p>
        </section>
        <section>
            <h2 class="font-serif text-xl font-semibold text-ivoire">7. Données personnelles</h2>
            <p class="mt-2">Les coordonnées collectées servent à traiter la commande et à assurer le service client. Elles sont conservées avec l’accord du client et gérées conformément aux règles applicables. Pour demander leur suppression : [à compléter].</p>
        </section>
    </div>
</div>
@endsection