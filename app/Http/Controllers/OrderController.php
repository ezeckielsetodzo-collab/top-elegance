<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $ouverts = Product::with('colors')
            ->where('preorder_open', true)
            ->orderBy('sort_order')
            ->get()
            ->map->toFrontend()
            ->all();

        return view('orders.create', [
            'ouverts' => $ouverts,
            'choix' => $request->only(['modele', 'couleur']),
        ]);
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $product = $request->product();
        $color = $product->colors()->where('name', $data['couleur'])->firstOrFail();
        $size = $product->sizes()->where('name', $data['taille'])->firstOrFail();
        $quantity = (int) $data['quantite'];
        $phone = preg_replace('/[^0-9+]/', '', $data['telephone']);
        $ambassador = $request->ambassador();
        $sponsor = $request->sponsor();
        $subtotal = (int) $product->price * $quantity;
        $discount = $ambassador ? (int) round($subtotal * $ambassador->discount_percent / 100) : 0;

        $order = DB::transaction(function () use ($data, $product, $color, $size, $quantity, $phone, $ambassador, $sponsor, $subtotal, $discount) {
            $customer = Customer::firstOrNew(['phone' => $phone]);
            $customer->name = $data['nom'];

            if (! $customer->consent) {
                $customer->consent = true;
                $customer->consented_at = now();
            }

            // Parrainage : seulement pour un nouveau client (jamais déjà parrainé ni déjà client).
            if ($sponsor && ! $customer->exists) {
                $customer->referred_by = $sponsor->id;
            }

            if (! $customer->referral_code) {
                $customer->referral_code = Customer::newReferralCode();
            }

            $customer->save();

            $order = Order::create([
                'customer_id' => $customer->id,
                'ambassador_id' => $ambassador?->id,
                'status' => 'nouvelle',
                'delivery_address' => $data['quartier'],
                'total' => $subtotal - $discount,
                'discount' => $discount,
                'terms_accepted_at' => now(),
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'color_id' => $color->id,
                'size_id' => $size->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
            ]);

            return $order;
        });

        session(['last_order_id' => $order->id]);

        return redirect()->route('orders.confirmation');
    }

    public function confirmation()
    {
        $orderId = session('last_order_id');

        if (! $orderId) {
            return redirect()->route('products.index');
        }

        $order = Order::with(['customer', 'items.product', 'items.color', 'items.size'])
            ->findOrFail($orderId);

        $item = $order->items->first();

        // Même format que celui attendu par la vue de confirmation.
        $commande = [
            'nom' => $order->customer->name,
            'telephone' => $order->customer->phone,
            'quartier' => $order->delivery_address,
            'modele' => $item->product->name,
            'couleur' => $item->color->name,
            'taille' => $item->size->name,
            'quantite' => $item->quantity,
            'code_parrainage' => $order->customer->referral_code,
        ];

        return view('orders.confirmation', ['commande' => $commande]);
    }
}
