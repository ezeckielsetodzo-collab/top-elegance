<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ambassador;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminDocumentController extends Controller
{
    public function order(Order $order)
    {
        return view('admin.documents.order', ['order' => $this->charger($order)]);
    }

    public function delivery(Order $order)
    {
        return view('admin.documents.delivery', ['order' => $this->charger($order)]);
    }

    public function ambassador(Request $request, Ambassador $ambassador)
    {
        $mois = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $request->query('mois'))
            ? $request->query('mois')
            : now()->format('Y-m');

        $debut = Carbon::createFromFormat('Y-m-d', $mois.'-01')->startOfMonth();

        $commandes = $ambassador->orders()
            ->with('customer')
            ->where('status', '!=', 'annulee')
            ->whereBetween('created_at', [$debut, $debut->copy()->endOfMonth()])
            ->orderBy('created_at')
            ->get();

        $ventes = (int) $commandes->sum('total');

        return view('admin.documents.ambassador', [
            'ambassador' => $ambassador,
            'commandes' => $commandes,
            'ventes' => $ventes,
            'commission' => (int) round($ventes * $ambassador->commission_percent / 100),
            'titreMois' => $debut->translatedFormat('F Y'),
        ]);
    }

    private function charger(Order $order): Order
    {
        return $order->load(['customer', 'ambassador', 'items.product', 'items.color', 'items.size']);
    }
}
