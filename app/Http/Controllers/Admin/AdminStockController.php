<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class AdminStockController extends Controller
{
    public function index()
    {
        $jours = (int) config('shop.dormant_days', 30);
        $limite = now()->subDays($jours);

        $derniereCommande = OrderItem::selectRaw('product_id, max(created_at) as derniere')
            ->groupBy('product_id')
            ->pluck('derniere', 'product_id');

        $totaux = Stock::selectRaw('product_id, sum(quantity) as total')
            ->groupBy('product_id')
            ->pluck('total', 'product_id');

        $lignes = Product::orderBy('sort_order')->get()->map(function (Product $product) use ($totaux, $derniereCommande, $limite) {
            $total = (int) ($totaux[$product->id] ?? 0);
            $derniere = isset($derniereCommande[$product->id]) ? \Illuminate\Support\Carbon::parse($derniereCommande[$product->id]) : null;

            return [
                'product' => $product,
                'total' => $total,
                'derniere' => $derniere,
                'dormant' => $total > 0 && (! $derniere || $derniere->lt($limite)),
            ];
        });

        return view('admin.stock.index', [
            'lignes' => $lignes,
            'jours' => $jours,
        ]);
    }

    public function edit(Product $product)
    {
        $product->load(['colors', 'sizes']);

        $quantites = $product->stocks()->get()->mapWithKeys(
            fn (Stock $stock) => [$stock->color_id.'-'.$stock->size_id => $stock->quantity]
        );

        return view('admin.stock.edit', [
            'product' => $product,
            'quantites' => $quantites,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->load(['colors', 'sizes']);

        $request->validate([
            'quantites' => ['nullable', 'array'],
            'quantites.*.*' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ], [
            'quantites.*.*.integer' => 'Chaque quantité doit être un nombre entier.',
            'quantites.*.*.min' => 'Une quantité ne peut pas être négative.',
        ]);

        $saisies = $request->input('quantites', []);

        foreach ($product->colors as $color) {
            foreach ($product->sizes as $size) {
                $valeur = $saisies[$color->id][$size->id] ?? null;

                if ($valeur === null || $valeur === '') {
                    continue;
                }

                Stock::updateOrCreate(
                    ['product_id' => $product->id, 'color_id' => $color->id, 'size_id' => $size->id],
                    ['quantity' => (int) $valeur]
                );
            }
        }

        return redirect()->route('admin.stock.index')->with('success', "{$product->name} : stock mis à jour.");
    }
}
