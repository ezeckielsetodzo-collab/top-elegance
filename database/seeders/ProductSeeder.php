<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $colors = collect([
            ['Noir', '#000000'],
            ['Ivoire', '#F5F0E6'],
            ['Or', '#C9A227'],
            ['Gris', '#808080'],
            ['Beige', '#D9C7A3'],
            ['Vert profond', '#0B3D2E'],
        ])->map(fn ($c) => Color::firstOrCreate(
            ['slug' => Str::slug($c[0])],
            ['name' => $c[0], 'hex' => $c[1]]
        ));

        $sizes = collect(['S', 'M', 'L', 'XL', 'XXL'])->map(
            fn ($name, $i) => Size::firstOrCreate(['name' => $name], ['sort_order' => $i])
        );

        // TODO : remplacer les 7 noms « Modèle N » et les prix par les vrais
        // (document DROP 01 et prix définitifs du propriétaire).
        $models = [
            ['TE Signature', true],
            ['Black Luxury', true],
            ['228 Edition', true],
            ['Modèle 04', false],
            ['Modèle 05', false],
            ['Modèle 06', false],
            ['Modèle 07', false],
            ['Modèle 08', false],
            ['Modèle 09', false],
            ['Modèle 10', false],
        ];

        foreach ($models as $i => [$name, $open]) {
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category' => 'tshirt',
                    'name' => $name,
                    'price' => 10000, // provisoire, en FCFA
                    'is_limited' => true,
                    'edition_number' => 1,
                    'preorder_open' => $open,
                    'sort_order' => $i + 1,
                ]
            );

            $product->colors()->sync($colors->random(random_int(2, 3))->pluck('id'));
            $product->sizes()->sync($sizes->pluck('id'));
        }
    }
}
