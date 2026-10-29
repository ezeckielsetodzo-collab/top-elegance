<?php

namespace App\Http\Controllers;

use App\Models\ColorVote;
use App\Models\Product;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PollController extends Controller
{
    /**
     * Résultats du sondage d'un modèle : nombre de votes par couleur.
     */
    public function results(Product $product): JsonResponse
    {
        return response()->json($this->resultats($product));
    }

    public function vote(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'modele' => ['required', 'string', 'exists:products,slug'],
            'couleur' => ['required', 'string', 'max:30'],
            'site_web' => ['nullable', 'max:0'],
        ], [
            'required' => 'Ce champ est obligatoire.',
            'exists' => 'Ce modèle n’existe pas.',
        ]);

        $product = Product::where('slug', $data['modele'])->firstOrFail();
        $color = $product->colors()->where('name', $data['couleur'])->first();

        if (! $color) {
            return $this->reponse($request, $product, false, 'Cette couleur n’existe pas pour ce modèle.', 422);
        }

        $empreinte = hash_hmac('sha256', $request->ip().'|'.$request->userAgent(), (string) config('app.key'));

        try {
            ColorVote::create([
                'product_id' => $product->id,
                'color_id' => $color->id,
                'voter_hash' => $empreinte,
            ]);
        } catch (UniqueConstraintViolationException) {
            return $this->reponse($request, $product, false, 'Vous avez déjà voté pour ce modèle.', 409);
        }

        return $this->reponse($request, $product, true, 'Merci pour votre vote !', 200);
    }

    private function reponse(Request $request, Product $product, bool $ok, string $message, int $statut): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['ok' => $ok, 'message' => $message] + $this->resultats($product), $statut);
        }

        return back()->with($ok ? 'sondage_ok' : 'sondage_erreur', $message);
    }

    private function resultats(Product $product): array
    {
        $votes = ColorVote::where('product_id', $product->id)
            ->selectRaw('color_id, count(*) as total')
            ->groupBy('color_id')
            ->pluck('total', 'color_id');

        $resultats = [];

        foreach ($product->colors as $color) {
            $resultats[$color->name] = (int) ($votes[$color->id] ?? 0);
        }

        return ['modele' => $product->slug, 'resultats' => $resultats, 'total' => array_sum($resultats)];
    }
}
