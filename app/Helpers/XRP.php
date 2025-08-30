<?php

namespace App\Helpers;

use App\Models\XrpPrice;
use Illuminate\Support\Facades\Storage;

class XRP
{
    /**
     * Get the latest XRP/USD rate from database.
     */
    public static function getRate(): float
    {
        $latest = XrpPrice::latest('created_at')->first();

        if (!$latest) {
            throw new \RuntimeException('No XRP price available');
        }

        return (float) $latest->price_usd;
    }

    /**
     * Convert XRP to USD.
     */
    public static function toUsd(float $xrp): float
    {
        $rate = self::getRate();
        return round($xrp * $rate, 8);
    }

    /**
     * Convert USD cents to XRP.
     */
    public static function fromUsdCents(int $usdCents): float
    {
        $rate = self::getRate();
        $usd = $usdCents / 100;
        return round($usd / $rate, 8);
    }
}
