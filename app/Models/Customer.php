<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'city', 'consent', 'consented_at', 'referral_code', 'referred_by'];

    protected $casts = [
        'consent' => 'boolean',
        'consented_at' => 'datetime',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(self::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(self::class, 'referred_by');
    }

    /**
     * Code de parrainage unique, lisible (sans 0, O, 1, I, L), ex. « PK7M2X ».
     */
    public static function newReferralCode(): string
    {
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        do {
            $code = 'P';

            for ($i = 0; $i < 5; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Nombre d'achats (commandes payées ou livrées) à partir duquel un client est dit « fidèle ».
     */
    public static function loyalThreshold(): int
    {
        return max(1, (int) config('shop.loyal_after', 3));
    }
}
