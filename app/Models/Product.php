<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'dimensions' => 'array',
            'certifications_available' => 'array',
            'is_cut_to_length' => 'boolean',
            'has_restlaengen' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'is_available_for_sale' => 'boolean',
            'is_partner_network' => 'boolean',
            'erp_synced_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function anarbeitungOptions(): BelongsToMany
    {
        return $this->belongsToMany(AnarbeitungOption::class, 'product_anarbeitung');
    }

    public function calculateWeight(int $quantity, ?int $lengthMm = null): float
    {
        if ($this->is_cut_to_length && $lengthMm) {
            return ($lengthMm / 1000) * $this->weight_per_meter_kg * $quantity;
        }
        return $this->weight_per_piece_kg * $quantity;
    }

    public function calculateBasePrice(int $quantity, ?int $lengthMm = null): float
    {
        $weight = $this->calculateWeight($quantity, $lengthMm);
        return $weight * $this->price_per_kg_eur;
    }

    /**
     * Whether product is physically in our warehouse.
     */
    public function isLagerware(): bool
    {
        return $this->stock_quantity_kg > 0;
    }

    /**
     * Bestellware = not in our warehouse, sourced from supplier on order.
     */
    public function isBestellware(): bool
    {
        return $this->stock_quantity_kg <= 0;
    }

    public function getStockStatus(): string
    {
        if ($this->stock_quantity_kg > 500) {
            return 'Ab Lager';
        } elseif ($this->stock_quantity_kg > 0) {
            return 'Geringe Menge';
        }
        return 'Bestellware';
    }

    public function getStockColor(): string
    {
        if ($this->stock_quantity_kg > 500) {
            return 'green';
        } elseif ($this->stock_quantity_kg > 0) {
            return 'yellow';
        }
        return 'gray';
    }

    /**
     * Estimated delivery in Werktage from order confirmation.
     */
    public function getDeliveryDays(): array
    {
        if ($this->stock_quantity_kg > 500) {
            return [1, 3];
        } elseif ($this->stock_quantity_kg > 0) {
            return [2, 4];
        }
        // Bestellware: sourced from supplier, then shipped from our warehouse
        return [5, 10];
    }

    public function getDeliveryLabel(): string
    {
        [$min, $max] = $this->getDeliveryDays();
        if ($this->isBestellware()) {
            return "Bestellware — Lieferfrist ca. {$min}–{$max} Werktage";
        }
        return "Ab Lager Weingarten — Lieferung in {$min}–{$max} Werktagen";
    }

    /**
     * Get customer-specific price per kg.
     */
    public function getPriceForCustomer(?Customer $customer): float
    {
        if (!$customer) return $this->price_per_kg_eur;
        return $customer->getPrice($this->price_per_kg_eur);
    }

    /**
     * Cross-sell: products in the same category or with the same material, excluding self.
     */
    public function getRelatedProducts(int $limit = 4)
    {
        // Include sibling categories (same parent) for broader matches
        $categoryIds = [$this->category_id];
        $cat = $this->category;
        if ($cat && $cat->parent_id) {
            $siblingIds = Category::where('parent_id', $cat->parent_id)->pluck('id')->toArray();
            $categoryIds = array_merge($categoryIds, $siblingIds);
        }

        return static::where('id', '!=', $this->id)
            ->where('is_active', true)
            ->where('is_available_for_sale', true)
            ->where(function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds)
                  ->orWhere('material_id', $this->material_id);
            })
            ->with('material', 'form', 'category')
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Simulated supplier sourcing info for Bestellware.
     * In production this would come from live supplier API queries.
     */
    public function getSupplierSourcing(): ?array
    {
        if (!$this->isBestellware()) return null;

        $supplierName = $this->supplier_name ?? 'Stahlwerk Süd GmbH';

        // Simulate 3 supplier quotes — the "best" is always chosen
        $basePrice = $this->price_per_kg_eur;
        return [
            'selected' => [
                'name' => $supplierName,
                'price_per_kg' => round($basePrice * 0.82, 2),
                'delivery_days' => rand(4, 6),
            ],
            'alternatives' => [
                ['name' => 'Salzgitter Mannesmann', 'price_per_kg' => round($basePrice * 0.85, 2), 'delivery_days' => rand(5, 8)],
                ['name' => 'ArcelorMittal Distribution', 'price_per_kg' => round($basePrice * 0.88, 2), 'delivery_days' => rand(6, 10)],
            ],
            'savings_pct' => 7,
        ];
    }

    /**
     * Whether this product's data originates from the seller ERP system.
     * All products come from ERP — Lagerware is stocked, Bestellware is supplier-sourced.
     */
    public function getErpSourceLabel(): string
    {
        if ($this->isLagerware()) {
            return 'ERP — Eigenlager';
        }
        if ($this->is_partner_network) {
            return 'ERP — Partnernetzwerk (' . ($this->partner_source ?? 'unbekannt') . ')';
        }
        return 'ERP — Kernsortiment Lieferant' . ($this->supplier_name ? ' (' . $this->supplier_name . ')' : '');
    }

    /**
     * AI-driven price suggestions based on simulated market analysis.
     * In production this would call an ML pricing service.
     */
    public function getAiPriceSuggestions(): array
    {
        $currentPrice = $this->price_per_kg_eur;
        $erpPrice = $this->erp_price_per_kg ?? round($currentPrice * 0.82, 4);
        $materialGrade = $this->material->grade ?? 'S235JR';

        // Simulated market index factors by material family
        $marketFactors = [
            'S235' => 1.03, 'S355' => 1.05, '1.4301' => 0.98, '1.4571' => 1.01,
            'DC01' => 1.02, 'P265' => 1.04, 'E235' => 1.00, 'EN AW' => 0.97,
            'CW614' => 1.06, 'Cu-DHP' => 1.08, '51CrV4' => 1.02,
        ];

        $factor = 1.00;
        foreach ($marketFactors as $prefix => $f) {
            if (str_starts_with($materialGrade, $prefix)) {
                $factor = $f;
                break;
            }
        }

        // Demand signal based on stock status
        $demandMultiplier = 1.00;
        $demandSignal = 'Neutral';
        if ($this->stock_quantity_kg > 5000) {
            $demandMultiplier = 0.97;
            $demandSignal = 'Überbestand — Abverkauf empfohlen';
        } elseif ($this->stock_quantity_kg > 2000) {
            $demandMultiplier = 1.00;
            $demandSignal = 'Normaler Bestand';
        } elseif ($this->stock_quantity_kg > 0) {
            $demandMultiplier = 1.03;
            $demandSignal = 'Geringer Bestand — Preiserhöhung möglich';
        } else {
            $demandMultiplier = 1.01;
            $demandSignal = 'Bestellware — marktüblicher Aufschlag';
        }

        // Competitor price simulation (±5% around current)
        $competitorAvg = round($currentPrice * (0.97 + (crc32($this->sku) % 10) / 100), 2);
        $competitorRange = [
            round($competitorAvg * 0.95, 2),
            round($competitorAvg * 1.05, 2),
        ];

        // Calculate suggested prices for different margin targets
        $targetMargins = [
            'konservativ' => ['label' => 'Konservativ (22 % Marge)', 'margin' => 0.22],
            'optimal' => ['label' => 'Optimal (18 % Marge)', 'margin' => 0.18],
            'wettbewerb' => ['label' => 'Wettbewerbsfähig (14 % Marge)', 'margin' => 0.14],
        ];

        $suggestions = [];
        foreach ($targetMargins as $key => $target) {
            $baseCalc = $erpPrice / (1 - $target['margin']);
            $marketAdjusted = round($baseCalc * $factor * $demandMultiplier, 2);
            $diffPct = $currentPrice > 0 ? round(($marketAdjusted - $currentPrice) / $currentPrice * 100, 1) : 0;
            $actualMargin = $marketAdjusted > 0 ? round(($marketAdjusted - $erpPrice) / $marketAdjusted * 100, 1) : 0;

            $suggestions[$key] = [
                'label' => $target['label'],
                'price' => $marketAdjusted,
                'diff_pct' => $diffPct,
                'actual_margin' => $actualMargin,
                'is_recommended' => $key === 'optimal',
            ];
        }

        // Confidence score based on data quality
        $confidence = 72;
        if ($this->erp_price_per_kg) $confidence += 10;
        if ($this->stock_quantity_kg > 0) $confidence += 8;
        if ($this->supplier_name) $confidence += 5;
        $confidence = min(95, $confidence);

        return [
            'suggestions' => $suggestions,
            'market_index' => [
                'factor' => $factor,
                'trend' => $factor >= 1.02 ? 'steigend' : ($factor <= 0.98 ? 'fallend' : 'stabil'),
                'label' => 'Marktindex ' . $materialGrade,
            ],
            'demand' => [
                'signal' => $demandSignal,
                'multiplier' => $demandMultiplier,
            ],
            'competitor' => [
                'avg' => $competitorAvg,
                'range' => $competitorRange,
            ],
            'erp_cost' => $erpPrice,
            'current_margin' => $currentPrice > 0 ? round(($currentPrice - $erpPrice) / $currentPrice * 100, 1) : 0,
            'confidence' => $confidence,
            'last_analysis' => now()->subHours(rand(1, 12))->format('d.m.Y H:i'),
        ];
    }
}
