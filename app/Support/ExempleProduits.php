<?php

namespace App\Support;

/**
 * Données d'exemple TEMPORAIRES pour le frontend.
 * À supprimer quand le ProductController du Dev 2 enverra les vrais produits.
 */
class ExempleProduits
{
    public static function tous(): array
    {
        $produits = [
            ['slug' => 'te-signature', 'name' => 'TE Signature', 'tagline' => 'Sobre, élégant, intemporel', 'description' => 'T-shirt noir avec monogramme TE sur la poitrine, logo TOP ÉLÉGANCE discret au dos et étiquette personnalisée. Broderie TE pour un rendu premium.', 'color' => '#161616', 'colors' => ['Noir', 'Blanc', 'Gris'], 'preorder_open' => true, 'is_limited' => false],
            ['slug' => 'lome-edition', 'name' => 'Lomé Edition', 'tagline' => 'Racines, fierté, Togo', 'description' => 'T-shirt blanc avec TOP ÉLÉGANCE devant et LOMÉ • 228 au dos, avec un petit rappel du drapeau togolais.', 'color' => '#f4f1ea', 'colors' => ['Blanc', 'Noir', 'Beige'], 'preorder_open' => false, 'is_limited' => false],
            ['slug' => 'black-luxury', 'name' => 'Black Luxury', 'tagline' => 'Élégance, minimaliste, premium', 'description' => 'T-shirt noir avec logo TE doré et détails très discrets, pour une image haut de gamme. Broderie ou impression premium.', 'color' => '#101010', 'colors' => ['Noir', 'Blanc', 'Doré'], 'preorder_open' => true, 'is_limited' => false],
            ['slug' => 'oversize-royal', 'name' => 'Oversize Royal', 'tagline' => 'Confort, modernité, caractère', 'description' => 'Coupe oversize beige avec petit TE devant et grand monogramme au dos. Coton épais, coupe structurée et étiquette extérieure.', 'color' => '#d9c4a0', 'colors' => ['Beige', 'Noir'], 'preorder_open' => false, 'is_limited' => false],
            ['slug' => 'african-elegance', 'name' => 'African Elegance', 'tagline' => 'Culture, identité, modernité', 'description' => 'T-shirt noir avec détails graphiques inspirés de motifs africains, utilisés avec sobriété autour du logo.', 'color' => '#141414', 'colors' => ['Noir', 'Blanc', 'Vert'], 'preorder_open' => false, 'is_limited' => false],
            ['slug' => 'te-01', 'name' => 'TE 01', 'tagline' => 'Ambition, progression, évolution', 'description' => 'T-shirt gris avec TE 01 en grand au dos, petit TE devant et signature TOP ÉLÉGANCE.', 'color' => '#8a8a88', 'colors' => ['Gris', 'Noir', 'Blanc'], 'preorder_open' => false, 'is_limited' => true],
            ['slug' => 'no-rules', 'name' => 'No Rules', 'tagline' => 'Liberté, style, élégance', 'description' => 'T-shirt blanc avec petit TE devant et la phrase « L’élégance n’a pas de limite » au dos.', 'color' => '#f4f1ea', 'colors' => ['Blanc', 'Noir', 'Gris'], 'preorder_open' => false, 'is_limited' => false],
            ['slug' => 'minimal-te', 'name' => 'Minimal TE', 'tagline' => 'Simple, classique, efficace', 'description' => 'T-shirt noir très épuré avec uniquement le monogramme TE et une petite étiquette de marque.', 'color' => '#101010', 'colors' => ['Noir', 'Blanc', 'Gris'], 'preorder_open' => false, 'is_limited' => false],
            ['slug' => '228-edition', 'name' => '228 Edition', 'tagline' => 'Togo, unité, fierté', 'description' => 'T-shirt vert profond avec 228 en grand au dos, TOP ÉLÉGANCE dessous et petit rappel du drapeau.', 'color' => '#0f4a2c', 'colors' => ['Vert', 'Blanc', 'Noir'], 'preorder_open' => true, 'is_limited' => false],
            ['slug' => 'founder-edition', 'name' => 'Founder Edition', 'tagline' => 'Exclusivité, prestige, vision', 'description' => 'T-shirt noir premium avec TE doré, TOP ÉLÉGANCE et EST. 2026.', 'color' => '#0e0e0e', 'colors' => ['Noir', 'Doré', 'Blanc'], 'preorder_open' => false, 'is_limited' => true],
        ];

        return array_map(fn ($p) => $p + ['url' => route('products.show', $p['slug'])], $produits);
    }

    public static function trouver(string $slug): ?array
    {
        foreach (self::tous() as $produit) {
            if ($produit['slug'] === $slug) {
                return $produit;
            }
        }

        return null;
    }
}