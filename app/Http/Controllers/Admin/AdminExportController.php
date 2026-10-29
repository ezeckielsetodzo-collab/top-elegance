<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Support\MonthlyIndicators;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminExportController extends Controller
{
    public function indicators(Request $request): StreamedResponse
    {
        $mois = $this->mois($request);
        $i = MonthlyIndicators::pourMois($mois)->calculer();

        $lignes = [
            ['Indicateur', 'Valeur'],
            ['Mois', $mois],
            ['Chiffre d\'affaires (FCFA)', $i['ca']],
            ['Marge (FCFA)', $i['marge']],
            ['Commandes', $i['commandes']],
            ['Panier moyen (FCFA)', $i['panier_moyen']],
            ['Clients du mois', $i['clients']],
            ['Nouveaux clients', $i['nouveaux_clients']],
            ['Taux de réachat (%)', $i['taux_reachat'] ?? ''],
            ['Réductions accordées (FCFA)', $i['reductions']],
            ['Commissions ambassadeurs (FCFA)', $i['commissions']],
            ['Coût d\'acquisition (FCFA)', $i['cout_acquisition'] ?? ''],
            ['Produit le plus vendu', $i['meilleur_produit']['nom'] ?? ''],
            ['Modèles en stock dormant', $i['stocks_dormants']],
        ];

        $lignes[] = [];
        $lignes[] = ['Ambassadeur', 'Code', 'Commandes', 'Ventes (FCFA)', 'Commission (FCFA)'];

        foreach ($i['ambassadeurs'] as $a) {
            $lignes[] = [$a['nom'], $a['code'], $a['commandes'], $a['ventes'], $a['commission']];
        }

        return $this->csv("indicateurs-{$mois}.csv", $lignes);
    }

    public function orders(Request $request): StreamedResponse
    {
        $mois = $this->mois($request);
        $debut = Carbon::createFromFormat('Y-m-d', $mois.'-01')->startOfMonth();

        $commandes = Order::with(['customer', 'ambassador', 'items.product', 'items.color', 'items.size'])
            ->whereBetween('created_at', [$debut, $debut->copy()->endOfMonth()])
            ->orderBy('id')
            ->get();

        $lignes = [['N°', 'Date', 'Statut', 'Client', 'Téléphone', 'Adresse', 'Articles', 'Code promo', 'Réduction (FCFA)', 'Total (FCFA)']];

        foreach ($commandes as $commande) {
            $articles = $commande->items->map(
                fn ($item) => "{$item->quantity} x {$item->product->name} ({$item->color->name}, {$item->size->name})"
            )->implode(' ; ');

            $lignes[] = [
                $commande->id,
                $commande->created_at->format('d/m/Y H:i'),
                Order::STATUSES[$commande->status] ?? $commande->status,
                $commande->customer->name,
                $commande->customer->phone,
                $commande->delivery_address,
                $articles,
                $commande->ambassador?->code,
                $commande->discount,
                $commande->total,
            ];
        }

        return $this->csv("commandes-{$mois}.csv", $lignes);
    }

    private function mois(Request $request): string
    {
        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $request->query('mois'))
            ? $request->query('mois')
            : now()->format('Y-m');
    }

    /**
     * CSV lisible par Excel en français : séparateur « ; » et BOM UTF-8.
     */
    private function csv(string $nom, array $lignes): StreamedResponse
    {
        return response()->streamDownload(function () use ($lignes) {
            $flux = fopen('php://output', 'w');
            fwrite($flux, "\xEF\xBB\xBF");

            foreach ($lignes as $ligne) {
                fputcsv($flux, array_map([$this, 'proteger'], $ligne), ';');
            }

            fclose($flux);
        }, $nom, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Empêche qu'un texte saisi par un client (ex. « =CMD(...) ») soit exécuté comme formule dans Excel.
     */
    private function proteger(mixed $valeur): mixed
    {
        if (is_string($valeur) && $valeur !== '' && in_array($valeur[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'".$valeur;
        }

        return $valeur;
    }
}
