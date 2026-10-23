<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('statut');

        $orders = Order::with(['customer', 'items.product', 'items.color', 'items.size'])
            ->when(
                array_key_exists((string) $status, Order::STATUSES),
                fn ($query) => $query->where('status', $status)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.orders.index', [
            'orders' => $orders,
            'counts' => $counts,
            'status' => $status,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
        ]);

        $order->update($data);

        return back()->with('success', "Commande n°{$order->id} : statut mis à jour.");
    }
}