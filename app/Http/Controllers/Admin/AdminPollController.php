<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ColorVote;
use App\Models\Product;

class AdminPollController extends Controller
{
    public function index()
    {
        $votes = ColorVote::selectRaw('product_id, color_id, count(*) as total')
            ->groupBy('product_id', 'color_id')
            ->get()
            ->groupBy('product_id');

        $sondages = Product::with('colors')->orderBy('sort_order')->get()->map(function (Product $product) use ($votes) {
            $parCouleur = ($votes[$product->id] ?? collect())->pluck('total', 'color_id');
            $total = (int) $parCouleur->sum();

            $lignes = $product->colors->map(fn ($color) => [
                'couleur' => $color->name,
                'votes' => (int) ($parCouleur[$color->id] ?? 0),
                'part' => $total ? (int) round(($parCouleur[$color->id] ?? 0) / $total * 100) : 0,
            ])->sortByDesc('votes')->values();

            return ['product' => $product, 'total' => $total, 'lignes' => $lignes];
        })->filter(fn ($sondage) => $sondage['total'] > 0)->values();

        return view('admin.polls.index', ['sondages' => $sondages]);
    }
}
