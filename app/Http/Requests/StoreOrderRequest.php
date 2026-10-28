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
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9 ]{8,15}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'city' => ['nullable', 'string', 'max:100'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'color_id' => ['required', 'integer', 'exists:colors,id'],
            'size_id' => ['required', 'integer', 'exists:sizes,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:10'],
            'consent' => ['sometimes', 'accepted'],
            'terms' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Indiquez votre nom.',
            'phone.required' => 'Indiquez votre numéro de téléphone.',
            'phone.regex' => 'Le numéro de téléphone est invalide.',
            'email.email' => 'L’adresse email est invalide.',
            'product_id.required' => 'Choisissez un modèle.',
            'color_id.required' => 'Choisissez une couleur.',
            'size_id.required' => 'Choisissez une taille.',
            'quantity.min' => 'La quantité minimale est 1.',
            'quantity.max' => 'La quantité maximale est 10.',
            'terms.accepted' => 'Vous devez accepter les conditions de vente.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $product = Product::find($this->integer('product_id'));

                if (! $product->preorder_open) {
                    $validator->errors()->add('product_id', 'Ce modèle n’est pas encore ouvert à la précommande.');
                }

                if (! $product->colors()->whereKey($this->integer('color_id'))->exists()) {
                    $validator->errors()->add('color_id', 'Cette couleur n’existe pas pour ce modèle.');
                }

                if (! $product->sizes()->whereKey($this->integer('size_id'))->exists()) {
                    $validator->errors()->add('size_id', 'Cette taille n’existe pas pour ce modèle.');
                }
            },
        ];
    }
}