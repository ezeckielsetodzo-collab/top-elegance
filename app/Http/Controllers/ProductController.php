<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('colors')
            ->orderBy('sort_order')
            ->get()
            ->map->toFrontend()
            ->all();

        return view('products.index', ['products' => $products]);
    }

    public function show(Product $product)
    {
        $product->load('colors');

        return view('products.show', ['product' => $product->toFrontend()]);
    }
}