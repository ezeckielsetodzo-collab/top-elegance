<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ambassador;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class AdminAmbassadorController extends Controller
{
    public function index(Request $request)
    {
        $mois = preg_match('/^\d{4}-\d{2}$/', (string) $request->query('mois'))
            ? $request->query('mois')
            : now()->format('Y-m');

        $debut = Carbon::createFromFormat('Y-m-d', $mois.'-01')->startOfMonth();
        $fin = $debut->copy()->endOfMonth();

        $ambassadeurs = Ambassador::orderBy('name')->get()->map(function (Ambassador $ambassador) use ($debut, $fin) {
            $commandes = $ambassador->orders()
                ->where('status', '!=', 'annulee')
                ->whereBetween('created_at', [$debut, $fin])
                ->get();

            $ventes = (int) $commandes->sum('total');

            return [
                'ambassador' => $ambassador,
                'commandes' => $commandes->count(),
                'ventes' => $ventes,
                'commission' => (int) round($ventes * $ambassador->commission_percent / 100),
            ];
        });

        return view('admin.ambassadors.index', [
            'lignes' => $ambassadeurs,
            'mois' => $mois,
            'moisPrecedent' => $debut->copy()->subMonth()->format('Y-m'),
            'moisSuivant' => $debut->copy()->addMonth()->format('Y-m'),
            'titreMois' => $debut->translatedFormat('F Y'),
        ]);
    }

    public function store(Request $request)
    {
        $request->merge(['code' => Ambassador::normalizeCode($request->input('code'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Z0-9]+$/', 'unique:ambassadors,code'],
            'discount_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'commission_percent' => ['required', 'integer', 'min:0', 'max:100'],
        ], $this->messages());

        Ambassador::create($data);

        return back()->with('success', "Ambassadeur {$data['name']} créé avec le code {$data['code']}.");
    }

    public function update(Request $request, Ambassador $ambassador)
    {
        $data = $request->validate([
            'discount_percent' => ['required', 'integer', 'min:0', 'max:100'],
            'commission_percent' => ['required', 'integer', 'min:0', 'max:100'],
        ], $this->messages());

        $ambassador->update($data + ['active' => $request->boolean('active')]);

        return back()->with('success', "{$ambassador->name} : mis à jour.");
    }

    private function messages(): array
    {
        return [
            'required' => 'Ce champ est obligatoire.',
            'integer' => 'Entrez un nombre entier.',
            'min' => 'La valeur est trop petite.',
            'max' => 'La valeur est trop grande.',
            'code.regex' => 'Le code ne doit contenir que des lettres et des chiffres (ex. JEAN10).',
            'code.unique' => 'Ce code existe déjà.',
        ];
    }
}
