<?php

namespace App\Helpers;

class ShippingHelper
{
    // PLZ zone 1 = near warehouse (Bodensee-Oberschwaben)
    // PLZ zone 2 = rest of Germany
    private const ZONE_1_PREFIXES = ['88', '87', '86', '89'];

    // Weight-based freight tiers: [max_kg => [zone1_price, zone2_price]]
    private const SPEDITION_TIERS = [
        100  => [69.00,  89.00],
        200  => [89.00,  119.00],
        300  => [109.00, 139.00],
        500  => [139.00, 169.00],
        700  => [169.00, 209.00],
        1000 => [199.00, 259.00],
        1500 => [229.00, 309.00],
        2000 => [259.00, 339.00],
        2500 => [289.00, 359.00],
        3000 => [319.00, 389.00],
        5000 => [399.00, 489.00],
    ];

    // Parcel tiers: [max_kg => price]
    private const PARCEL_TIERS = [
        5  => 5.90,
        10 => 8.90,
        15 => 11.90,
        20 => 13.90,
        30 => 16.90,
    ];

    private const LANGPAKET_TIERS = [
        10 => 29.90,
        30 => 39.90,
        60 => 69.90,
    ];

    private const EXPRESS_SURCHARGES = [
        'express_next_day' => ['label' => 'Express — nächster Werktag bis 18:00', 'parcel' => 15.00, 'spedition' => 35.00],
        'express_12'       => ['label' => 'Express — nächster Werktag bis 12:00', 'parcel' => 25.00, 'spedition' => 55.00],
    ];

    /**
     * Determine PLZ zone: 1 (near warehouse) or 2 (rest of Germany).
     */
    public static function getZone(string $postalCode): int
    {
        $prefix = substr($postalCode, 0, 2);
        return in_array($prefix, self::ZONE_1_PREFIXES) ? 1 : 2;
    }

    /**
     * Determine if the order qualifies for Frei-Haus regional delivery.
     */
    public static function isFreiHaus(string $postalCode): bool
    {
        return str_starts_with($postalCode, '88');
    }

    /**
     * Determine the auto-selected shipping method based on weight and item count.
     */
    public static function getMethod(float $totalWeightKg, int $itemCount): string
    {
        // Over 30 kg total or more than 3 items → Spedition
        if ($totalWeightKg > 30 || $itemCount > 3) {
            return 'spedition';
        }
        return 'parcel';
    }

    /**
     * Calculate parcel cost. Orders >30 kg are split into multiple parcels.
     */
    public static function calculateParcelCost(float $totalWeightKg): float
    {
        if ($totalWeightKg <= 0) return 0;

        // Split into 30 kg parcels
        $numParcels = (int) ceil($totalWeightKg / 30);
        $avgWeight = $totalWeightKg / $numParcels;

        $perParcel = self::PARCEL_TIERS[30]; // default
        foreach (self::PARCEL_TIERS as $maxKg => $price) {
            if ($avgWeight <= $maxKg) {
                $perParcel = $price;
                break;
            }
        }

        return $perParcel * $numParcels;
    }

    /**
     * Calculate Spedition (freight) cost based on weight and PLZ zone.
     */
    public static function calculateSpeditionCost(float $totalWeightKg, int $zone): float
    {
        if ($totalWeightKg <= 0) return 0;

        $zoneIndex = $zone - 1; // 0 or 1
        foreach (self::SPEDITION_TIERS as $maxKg => $prices) {
            if ($totalWeightKg <= $maxKg) {
                return $prices[$zoneIndex];
            }
        }

        // Over 5000 kg: last tier + per-ton surcharge
        $lastTier = end(self::SPEDITION_TIERS);
        $extraTons = ceil(($totalWeightKg - 5000) / 1000);
        return $lastTier[$zoneIndex] + ($extraTons * 85.00);
    }

    /**
     * Build all available shipping options for the checkout page.
     *
     * Returns an array of options, each with:
     *   code, label, description, cost, delivery_min, delivery_max, is_free
     */
    public static function getOptions(float $totalWeightKg, int $itemCount, string $postalCode): array
    {
        $options = [];
        $zone = self::getZone($postalCode);
        $method = self::getMethod($totalWeightKg, $itemCount);
        $isFreiHaus = self::isFreiHaus($postalCode);

        // 1. Standard delivery
        if ($method === 'parcel') {
            $baseCost = self::calculateParcelCost($totalWeightKg);
            $numParcels = (int) ceil($totalWeightKg / 30);
            $desc = $numParcels > 1
                ? "Paketversand (DHL/GLS), {$numParcels} Pakete"
                : 'Paketversand (DHL/GLS)';
            $options[] = [
                'code' => 'standard',
                'label' => 'Standard-Versand',
                'description' => $desc,
                'cost' => $isFreiHaus ? 0 : $baseCost,
                'delivery_min' => 2,
                'delivery_max' => 4,
                'is_free' => $isFreiHaus,
                'note' => $isFreiHaus ? 'Frei-Haus-Lieferung Region Bodensee-Oberschwaben' : null,
            ];
        } else {
            $baseCost = self::calculateSpeditionCost($totalWeightKg, $zone);
            $zoneLabel = $zone === 1 ? 'Region Süd' : 'Deutschlandweit';
            $options[] = [
                'code' => 'standard',
                'label' => 'Speditionsversand',
                'description' => "Stückgut per Spedition, frei Bordsteinkante ({$zoneLabel})",
                'cost' => $isFreiHaus ? 0 : $baseCost,
                'delivery_min' => 3,
                'delivery_max' => 5,
                'is_free' => $isFreiHaus,
                'note' => $isFreiHaus ? 'Frei-Haus-Lieferung Region Bodensee-Oberschwaben' : null,
            ];
        }

        // 2. Express next day (only if weight allows)
        if ($totalWeightKg <= 3000) {
            $surcharge = $method === 'parcel'
                ? self::EXPRESS_SURCHARGES['express_next_day']['parcel']
                : self::EXPRESS_SURCHARGES['express_next_day']['spedition'];
            $expressCost = ($isFreiHaus ? 0 : $options[0]['cost']) + $surcharge;

            $options[] = [
                'code' => 'express',
                'label' => 'Express — nächster Werktag',
                'description' => 'Zustellung am nächsten Werktag bis 18:00 Uhr',
                'cost' => $expressCost,
                'delivery_min' => 1,
                'delivery_max' => 1,
                'is_free' => false,
                'note' => 'Aufpreis ' . self::formatEur($surcharge) . ' für Expressversand',
            ];
        }

        // 3. Express by 12:00
        if ($totalWeightKg <= 1500) {
            $surcharge = $method === 'parcel'
                ? self::EXPRESS_SURCHARGES['express_12']['parcel']
                : self::EXPRESS_SURCHARGES['express_12']['spedition'];
            $expressCost = ($isFreiHaus ? 0 : $options[0]['cost']) + $surcharge;

            $options[] = [
                'code' => 'express_12',
                'label' => 'Express — bis 12:00 Uhr',
                'description' => 'Zustellung am nächsten Werktag bis 12:00 Uhr',
                'cost' => $expressCost,
                'delivery_min' => 1,
                'delivery_max' => 1,
                'is_free' => false,
                'note' => 'Aufpreis ' . self::formatEur($surcharge) . ' für Expressversand bis 12:00',
            ];
        }

        // 4. Self-pickup — always available
        $options[] = [
            'code' => 'pickup',
            'label' => 'Selbstabholung',
            'description' => 'Abholung am Lager Weingarten (Industriestraße 17), Mo–Fr 7:00–16:30',
            'cost' => 0,
            'delivery_min' => 1,
            'delivery_max' => 2,
            'is_free' => true,
            'note' => 'Terminabsprache erforderlich unter +49 751 3606-0',
        ];

        return $options;
    }

    /**
     * Calculate total weight of cart items.
     */
    public static function calculateCartWeight($items): float
    {
        $totalWeight = 0;
        foreach ($items as $item) {
            $totalWeight += $item->product->calculateWeight($item->quantity, $item->length_mm);
        }
        return $totalWeight;
    }

    /**
     * Get the cost for a specific shipping option code.
     */
    public static function getCostForOption(string $optionCode, float $totalWeightKg, int $itemCount, string $postalCode): float
    {
        $options = self::getOptions($totalWeightKg, $itemCount, $postalCode);
        foreach ($options as $opt) {
            if ($opt['code'] === $optionCode) {
                return $opt['cost'];
            }
        }
        // Fallback to standard
        return $options[0]['cost'] ?? 0;
    }

    private static function formatEur(float $value): string
    {
        return number_format($value, 2, ',', '.') . "\u{00A0}€";
    }
}
