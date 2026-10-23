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
        $products = Product::with(['colors', 'sizes'])
            ->where('preorder_open', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'slug' => $p->slug,
                'name' => $p->name,
                'price' => $p->price,
                'colors' => $p->colors->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])->values()->all(),
                'sizes' => $p->sizes->sortBy('sort_order')->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values()->all(),
            ])
            ->all();

        return view('orders.create', [
            'products' => $products,
            'selected' => $request->query('modele'),
        ]);
    }

    public function store(StoreOrderRequest $request)
    {
        $data = $request->validated();
        $product = Product::findOrFail($data['product_id']);
        $quantity = (int) $data['quantity'];

        $order = DB::transaction(function () use ($data, $product, $quantity) {
            $customer = Customer::firstOrNew(['phone' => $data['phone']]);
            $customer->name = $data['name'];
            $customer->email = $data['email'] ?? $customer->email;
            $customer->city = $data['city'] ?? $customer->city;

            if (! empty($data['consent']) && ! $customer->consent) {
                $customer->consent = true;
                $customer->consented_at = now();
            }

            $customer->save();

            $order = Order::create([
                'customer_id' => $customer->id,
                'status' => 'nouvelle',
                'total' => $product->price * $quantity,
                'terms_accepted_at' => now(),
            ]);

            $order->items()->create([
                'product_id' => $product->id,
                'color_id' => $data['color_id'],
                'size_id' => $data['size_id'],
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

        return view('orders.confirmation', [
            'order' => $order,
            'whatsappUrl' => $this->whatsappUrl($order),
        ]);
    }

    private function whatsappUrl(Order $order): string
    {
        $item = $order->items->first();

        $message = "Bonjour TOP ÉLÉGANCE, je souhaite précommander :\n"
            ."{$item->quantity} × {$item->product->name} ({$item->color->name}, taille {$item->size->name})\n"
            ."Commande n°{$order->id} — {$order->customer->name}, {$order->customer->phone}";

        $number = preg_replace('/\D/', '', (string) config('shop.whatsapp_number'));

        return 'https://wa.me/'.$number.'?text='.rawurlencode($message);
    }
}