<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function index()
    {
        return view('admin.products.index', [
            'products' => Product::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'price' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'cost' => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'preorder_open' => ['sometimes', 'boolean'],
        ], [
            'price.integer' => 'Le prix doit être un nombre entier (FCFA).',
            'cost.integer' => 'Le coût doit être un nombre entier (FCFA).',
            'price.min' => 'Le prix ne peut pas être négatif.',
            'cost.min' => 'Le coût ne peut pas être négatif.',
        ]);

        $product->update([
            'price' => $data['price'] ?? null,
            'cost' => $data['cost'] ?? null,
            'preorder_open' => $request->boolean('preorder_open'),
        ]);

        return back()->with('success', "{$product->name} : mis à jour.");
    }
}
