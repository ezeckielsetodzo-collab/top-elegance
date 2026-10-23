<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category', 'name', 'slug', 'tagline', 'description', 'display_color',
        'price', 'cost', 'is_limited', 'edition_number', 'preorder_open', 'sort_order',
    ];

    protected $casts = [
        'is_limited' => 'boolean',
        'preorder_open' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class);
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Format attendu par les vues Blade (carte produit, collection, fiche).
     */
    public function toFrontend(): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'tagline' => $this->tagline,
            'description' => $this->description,
            'color' => $this->display_color,
            'colors' => $this->colors->pluck('name')->all(),
            'preorder_open' => $this->preorder_open,
            'is_limited' => $this->is_limited,
            'url' => route('products.show', $this->slug),
        ];
    }
}