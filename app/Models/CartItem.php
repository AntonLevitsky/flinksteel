<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'anarbeitung' => 'array',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function recalculate(): void
    {
        $product = $this->product;
        $weight = $product->calculateWeight($this->quantity, $this->length_mm);
        $basePrice = $weight * $product->price_per_kg_eur;

        $anarbeitungCost = 0;
        if ($this->anarbeitung) {
            $options = AnarbeitungOption::whereIn('code', $this->anarbeitung)->get();
            foreach ($options as $option) {
                $anarbeitungCost += $option->calculateCost($weight, $this->quantity);
            }
        }

        $certificateCost = 0;
        if ($this->certificate_code) {
            $cert = Certificate::where('code', $this->certificate_code)->first();
            if ($cert) {
                $certificateCost = $cert->surcharge_eur;
            }
        }

        $this->unit_price_eur = $basePrice / max($this->quantity, 1);
        $this->line_total_eur = $basePrice + $anarbeitungCost + $certificateCost;
        $this->save();
    }
}
