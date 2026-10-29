<?php

namespace App\Support;

use App\Models\Ambassador;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Carbon;

class MonthlyIndicators
{
    private readonly Carbon $debut;

    public function __construct(Carbon $debut)
    {
        $this->debut = $debut->copy()->startOfMonth();
    }

    public static function pourMois(string $mois): self
    {
        return new self(Carbon::createFromFormat('Y-m-d', $mois.'-01')->startOfMonth());
    }

    public function calculer(): array
    {
        $debut = $this->debut;
        $fin = $debut->copy()->endOfMonth();

        $commandes = Order::with(['items.product', 'ambassador'])
            ->where('status', '!=', 'annulee')
            ->whereBetween('created_at', [$debut, $fin])
            ->get();

        $ca = (int) $commandes->sum('total');
        $reductions = (int) $commandes->sum('discount');

        $cout = 0;
        $coutsManquants = false;
        $parProduit = [];

        foreach ($commandes as $commande) {
            foreach ($commande->items as $item) {
                if ($item->product->cost === null) {
                    $coutsManquants = true;
                } else {
                    $cout += (int) $item->product->cost * $item->quantity;
                }

                $parProduit[$item->product_id] = ($parProduit[$item->product_id] ?? 0) + $item->quantity;
            }
        }

        $clientsDuMois = $commandes->pluck('customer_id')->unique();

        $clientsConnus = Order::where('status', '!=', 'annulee')
            ->where('created_at', '<', $debut)
            ->whereIn('customer_id', $clientsDuMois)
            ->distinct()
            ->pluck('customer_id');

        $nouveaux = $clientsDuMois->count() - $clientsConnus->count();

        $ambassadeurs = [];
        $commissions = 0;

        foreach ($commandes->whereNotNull('ambassador_id')->groupBy('ambassador_id') as $groupe) {
            /** @var Ambassador $ambassador */
            $ambassador = $groupe->first()->ambassador;
            $ventes = (int) $groupe->sum('total');
            $commission = (int) round($ventes * $ambassador->commission_percent / 100);
            $commissions += $commission;

            $ambassadeurs[] = [
                'nom' => $ambassador->name,
                'code' => $ambassador->code,
                'commandes' => $groupe->count(),
                'ventes' => $ventes,
                'commission' => $commission,
            ];
        }

        usort($ambassadeurs, fn ($a, $b) => $b['ventes'] <=> $a['ventes']);

        arsort($parProduit);
        $meilleurId = array_key_first($parProduit);

        return [
            'debut' => $debut,
            'commandes' => $commandes->count(),
            'ca' => $ca,
            'panier_moyen' => $commandes->count() ? (int) round($ca / $commandes->count()) : 0,
            'marge' => $ca - $cout,
            'couts_manquants' => $coutsManquants,
            'clients' => $clientsDuMois->count(),
            'nouveaux_clients' => $nouveaux,
            'taux_reachat' => $clientsDuMois->count() ? (int) round($clientsConnus->count() / $clientsDuMois->count() * 100) : null,
            'reductions' => $reductions,
            'commissions' => $commissions,
            'cout_acquisition' => $nouveaux > 0 ? (int) round(($commissions + $reductions) / $nouveaux) : null,
            'ambassadeurs' => $ambassadeurs,
            'meilleur_produit' => $meilleurId ? [
                'nom' => Product::find($meilleurId)?->name,
                'quantite' => $parProduit[$meilleurId],
            ] : null,
            'stocks_dormants' => $this->stocksDormants(),
        ];
    }

    /**
     * Nombre de modèles avec du stock mais sans commande depuis N jours (réglage SHOP_DORMANT_DAYS).
     */
    private function stocksDormants(): int
    {
        $limite = now()->subDays((int) config('shop.dormant_days', 30));

        $enStock = Stock::selectRaw('product_id, sum(quantity) as total')
            ->groupBy('product_id')
            ->having('total', '>', 0)
            ->pluck('product_id');

        $recents = OrderItem::whereIn('product_id', $enStock)
            ->where('created_at', '>=', $limite)
            ->distinct()
            ->pluck('product_id');

        return $enStock->diff($recents)->count();
    }
}
