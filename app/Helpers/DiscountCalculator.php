<?php

namespace App\Helpers;

class DiscountCalculator
{
    /**
     * Calculate the discounted unit price using cascading discounts.
     *
     * @param float $basePrice
     * @param array|null $discountSteps
     * @return float
     */
    public static function calculate(float $basePrice, ?array $discountSteps): float
    {
        if (empty($discountSteps)) {
            return round($basePrice, 2);
        }

        $discountedPrice = $basePrice;
        
        foreach ($discountSteps as $discount) {
            $discount = (float) $discount;
            if ($discount > 0 && $discount <= 100) {
                $discountedPrice *= (1 - ($discount / 100));
            }
        }

        return round($discountedPrice, 2);
    }

    /**
     * Calculate the effective discount percentage.
     *
     * @param float $basePrice
     * @param float $discountedPrice
     * @return float
     */
    public static function calculateEffectiveDiscountPercentage(float $basePrice, float $discountedPrice): float
    {
        if ($basePrice <= 0) {
            return 0.00;
        }

        $effectiveDiscount = (1 - ($discountedPrice / $basePrice)) * 100;
        return round($effectiveDiscount, 2);
    }
}
