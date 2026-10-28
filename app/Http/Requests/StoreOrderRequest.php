<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:100'],
            'telephone' => ['required', 'regex:/^[0-9 +().-]{8,20}$/'],
            'quartier' => ['required', 'string', 'max:150'],
            'modele' => ['required', 'string', 'exists:products,slug'],
            'couleur' => ['required', 'string', 'max:30'],
            'taille' => ['required', 'string', 'max:10'],
            'quantite' => ['required', 'integer', 'min:1', 'max:10'],
            'consentement' => ['accepted'],
            'cgv' => ['accepted'],
            // Piège à robots : ce champ invisible doit rester vide.
            'site_web' => ['nullable', 'max:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Ce champ est obligatoire.',
            'regex' => 'Entrez un numéro de téléphone valide.',
            'exists' => 'Ce choix n’est pas disponible.',
            'accepted' => 'Vous devez cocher cette case pour continuer.',
            'integer' => 'Entrez un nombre entier.',
            'min' => 'La valeur est trop petite.',
            'max' => 'La valeur est trop grande.',
        ];
    }

    /**
     * Le modèle choisi, retrouvé par son slug (valide après la validation).
     */
    public function product(): Product
    {
        return Product::where('slug', $this->input('modele'))->firstOrFail();
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $product = $this->product();

                if (! $product->preorder_open) {
                    $validator->errors()->add('modele', 'Ce modèle n’est pas encore ouvert à la précommande.');
                }

                if (! $product->colors()->where('name', $this->input('couleur'))->exists()) {
                    $validator->errors()->add('couleur', 'Cette couleur n’existe pas pour ce modèle.');
                }

                if (! $product->sizes()->where('name', $this->input('taille'))->exists()) {
                    $validator->errors()->add('taille', 'Cette taille n’existe pas pour ce modèle.');
                }
            },
        ];
    }
}
