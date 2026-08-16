<?php

namespace App\Support;

use App\Models\Setting;

final class PriceConverter
{
    public const DEFAULT_USD_TO_EGP_RATE = 51.5;

    public static function usdToEgpRate(): float
    {
        $rate = (float) Setting::get('usd_to_egp_rate', self::DEFAULT_USD_TO_EGP_RATE);

        return $rate > 0 ? $rate : self::DEFAULT_USD_TO_EGP_RATE;
    }

    public static function toEgp(float|int|string|null $amount, ?string $currency): ?float
    {
        if ($amount === null) {
            return null;
        }

        return match (strtoupper((string) $currency)) {
            'EGP' => round((float) $amount, 2),
            'USD' => round((float) $amount * self::usdToEgpRate(), 2),
            default => null,
        };
    }
}
