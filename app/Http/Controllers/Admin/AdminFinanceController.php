<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinanceEntry;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminFinanceController extends Controller
{
    public function index()
    {
        $solde = 0;
        $entrees = 0;
        $sorties = 0;

        $lignes = FinanceEntry::orderBy('entry_date')->orderBy('id')->get()->map(function (FinanceEntry $entry) use (&$solde, &$entrees, &$sorties) {
            $solde += $entry->income - $entry->expense;
            $entrees += $entry->income;
            $sorties += $entry->expense;

            return ['entry' => $entry, 'solde' => $solde];
        })->reverse()->values();

        $vendus = OrderItem::selectRaw('product_id, sum(quantity) as total')
            ->whereHas('order', fn ($query) => $query->where('status', '!=', 'annulee'))
            ->groupBy('product_id')
            ->pluck('total', 'product_id');

        $marges = Product::orderBy('sort_order')->get()->map(function (Product $product) use ($vendus) {
            $unite = ($product->price !== null && $product->cost !== null)
                ? (int) $product->price - (int) $product->cost
                : null;
            $quantite = (int) ($vendus[$product->id] ?? 0);

            return [
                'product' => $product,
                'unite' => $unite,
                'taux' => ($unite !== null && $product->price > 0) ? round($unite / $product->price * 100) : null,
                'quantite' => $quantite,
                'total' => $unite !== null ? $unite * $quantite : null,
            ];
        });

        return view('admin.finances.index', [
            'lignes' => $lignes,
            'solde' => $solde,
            'entrees' => $entrees,
            'sorties' => $sorties,
            'marges' => $marges,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'entry_date' => ['required', 'date'],
            'label' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:entree,sortie'],
            'amount' => ['required', 'integer', 'min:1', 'max:1000000000'],
        ], [
            'required' => 'Ce champ est obligatoire.',
            'date' => 'La date est invalide.',
            'integer' => 'Le montant doit être un nombre entier (FCFA).',
            'amount.min' => 'Le montant doit être supérieur à zéro.',
            'in' => 'Choisissez entrée ou sortie.',
        ]);

        FinanceEntry::create([
            'entry_date' => $data['entry_date'],
            'label' => $data['label'],
            'income' => $data['type'] === 'entree' ? $data['amount'] : 0,
            'expense' => $data['type'] === 'sortie' ? $data['amount'] : 0,
        ]);

        return back()->with('success', 'Écriture ajoutée.');
    }

    public function destroy(FinanceEntry $entry)
    {
        $entry->delete();

        return back()->with('success', 'Écriture supprimée.');
    }
}
