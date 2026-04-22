<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AnarbeitungOption extends Model
{
    protected $table = 'anarbeitung_options';
    protected $guarded = [];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_anarbeitung');
    }

    public function calculateCost(float $weightKg, int $quantity): float
    {
        if ($this->price_per_cut_eur) {
            return $this->price_per_cut_eur * $quantity;
        }
        if ($this->price_per_kg_eur) {
            return $this->price_per_kg_eur * $weightKg;
        }
        return 0;
    }
}
