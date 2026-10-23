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
        $palette = [
            'Noir' => '#111111',
            'Blanc' => '#f4f1ea',
            'Gris' => '#8a8a88',
            'Beige' => '#d9c4a0',
            'Vert' => '#0f4a2c',
            'Doré' => '#d4a84b',
        ];

        $colors = collect($palette)->map(fn ($hex, $name) => Color::updateOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name, 'hex' => $hex]
        ));

        $sizes = collect(['S', 'M', 'L', 'XL', 'XXL'])->map(
            fn ($name, $i) => Size::updateOrCreate(['name' => $name], ['sort_order' => $i])
        );

        // Prix provisoires (10 000 FCFA) : à remplacer par les prix définitifs du propriétaire.
        $models = [
            ['TE Signature', 'Sobre, élégant, intemporel', 'T-shirt noir avec monogramme TE sur la poitrine, logo TOP ÉLÉGANCE discret au dos et étiquette personnalisée. Broderie TE pour un rendu premium.', '#161616', ['Noir', 'Blanc', 'Gris'], true, false],
            ['Lomé Edition', 'Racines, fierté, Togo', 'T-shirt blanc avec TOP ÉLÉGANCE devant et LOMÉ • 228 au dos, avec un petit rappel du drapeau togolais.', '#f4f1ea', ['Blanc', 'Noir', 'Beige'], false, false],
            ['Black Luxury', 'Élégance, minimaliste, premium', 'T-shirt noir avec logo TE doré et détails très discrets, pour une image haut de gamme. Broderie ou impression premium.', '#101010', ['Noir', 'Blanc', 'Doré'], true, false],
            ['Oversize Royal', 'Confort, modernité, caractère', 'Coupe oversize beige avec petit TE devant et grand monogramme au dos. Coton épais, coupe structurée et étiquette extérieure.', '#d9c4a0', ['Beige', 'Noir'], false, false],
            ['African Elegance', 'Culture, identité, modernité', 'T-shirt noir avec détails graphiques inspirés de motifs africains, utilisés avec sobriété autour du logo.', '#141414', ['Noir', 'Blanc', 'Vert'], false, false],
            ['TE 01', 'Ambition, progression, évolution', 'T-shirt gris avec TE 01 en grand au dos, petit TE devant et signature TOP ÉLÉGANCE.', '#8a8a88', ['Gris', 'Noir', 'Blanc'], false, true],
            ['No Rules', 'Liberté, style, élégance', 'T-shirt blanc avec petit TE devant et la phrase « L’élégance n’a pas de limite » au dos.', '#f4f1ea', ['Blanc', 'Noir', 'Gris'], false, false],
            ['Minimal TE', 'Simple, classique, efficace', 'T-shirt noir très épuré avec uniquement le monogramme TE et une petite étiquette de marque.', '#101010', ['Noir', 'Blanc', 'Gris'], false, false],
            ['228 Edition', 'Togo, unité, fierté', 'T-shirt vert profond avec 228 en grand au dos, TOP ÉLÉGANCE dessous et petit rappel du drapeau.', '#0f4a2c', ['Vert', 'Blanc', 'Noir'], true, false],
            ['Founder Edition', 'Exclusivité, prestige, vision', 'T-shirt noir premium avec TE doré, TOP ÉLÉGANCE et EST. 2026.', '#0e0e0e', ['Noir', 'Doré', 'Blanc'], false, true],
        ];

        foreach ($models as $i => [$name, $tagline, $description, $displayColor, $colorNames, $open, $limited]) {
            $product = Product::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category' => 'tshirt',
                    'name' => $name,
                    'tagline' => $tagline,
                    'description' => $description,
                    'display_color' => $displayColor,
                    'price' => 10000,
                    'is_limited' => $limited,
                    'edition_number' => $limited ? 1 : null,
                    'preorder_open' => $open,
                    'sort_order' => $i + 1,
                ]
            );

            $product->colors()->sync($colors->only($colorNames)->pluck('id'));
            $product->sizes()->sync($sizes->pluck('id'));
        }
    }
}