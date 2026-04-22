<?php

namespace App\Helpers;

class PriceHelper
{
    public static function format(float $value): string
    {
        return number_format($value, 2, ',', '.') . "\u{00A0}€";
    }

    public static function formatNumber(float $value, int $decimals = 2): string
    {
        return number_format($value, $decimals, ',', '.');
    }
}
