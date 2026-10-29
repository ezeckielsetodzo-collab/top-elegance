<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    private const ACHATS = ['payee', 'livree'];

    public function index(Request $request)
    {
        $seuil = Customer::loyalThreshold();
        $fideles = $request->query('filtre') === 'fideles';

        $clients = Customer::query()
            ->with('referrer')
            ->withCount([
                'orders as commandes_count' => fn ($q) => $q->where('status', '!=', 'annulee'),
                'orders as achats_count' => fn ($q) => $q->whereIn('status', self::ACHATS),
                'referrals as filleuls_count',
            ])
            ->withSum(['orders as depense' => fn ($q) => $q->whereIn('status', self::ACHATS)], 'total')
            ->when($fideles, fn ($q) => $q->has('orders', '>=', 1)->whereHas(
                'orders',
                fn ($o) => $o->whereIn('status', self::ACHATS),
                '>=',
                $seuil
            ))
            ->orderByDesc('achats_count')
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return view('admin.customers.index', [
            'clients' => $clients,
            'seuil' => $seuil,
            'fideles' => $fideles,
        ]);
    }
}
