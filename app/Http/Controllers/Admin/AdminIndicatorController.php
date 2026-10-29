<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\MonthlyIndicators;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminIndicatorController extends Controller
{
    public function index(Request $request)
    {
        $mois = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $request->query('mois'))
            ? $request->query('mois')
            : now()->format('Y-m');

        $indicateurs = MonthlyIndicators::pourMois($mois)->calculer();
        $debut = Carbon::createFromFormat('Y-m-d', $mois.'-01');

        return view('admin.indicators.index', [
            'i' => $indicateurs,
            'titreMois' => $debut->translatedFormat('F Y'),
            'moisPrecedent' => $debut->copy()->subMonth()->format('Y-m'),
            'moisSuivant' => $debut->copy()->addMonth()->format('Y-m'),
        ]);
    }
}
