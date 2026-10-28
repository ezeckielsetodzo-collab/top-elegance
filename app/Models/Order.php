<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUSES = [
        'nouvelle' => 'Nouvelle',
        'confirmee' => 'Confirmée',
        'payee' => 'Payée',
        'livree' => 'Livrée',
        'annulee' => 'Annulée',
    ];

    protected $fillable = ['customer_id', 'status', 'delivery_address', 'total', 'terms_accepted_at', 'notes'];

    protected $casts = [
        'terms_accepted_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}