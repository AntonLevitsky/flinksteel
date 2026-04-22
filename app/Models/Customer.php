<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $guarded = [];

    public function wholesaler(): BelongsTo
    {
        return $this->belongsTo(Wholesaler::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the customer-specific price for a product.
     */
    public function getPrice(float $basePricePerKg): float
    {
        return round($basePricePerKg * $this->price_multiplier, 2);
    }

    public function getPriceTierLabel(): string
    {
        return match ($this->price_tier) {
            'premium' => 'Premium-Kunde',
            'vip' => 'VIP-Konditionen',
            default => 'Standardkonditionen',
        };
    }
}
