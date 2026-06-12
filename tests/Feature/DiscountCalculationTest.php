<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Helpers\DiscountCalculator;

class DiscountCalculationTest extends TestCase
{
    /**
     * Test that cascading discounts are calculated correctly.
     */
    public function test_cascading_discounts_calculate_correctly(): void
    {
        $basePrice = 100.00;
        
        // Example check from PRD: B = 100, LM [20, 20, 10] -> 57.6
        $discountSteps = [20, 20, 10];
        $result = DiscountCalculator::calculate($basePrice, $discountSteps);
        
        $this->assertEquals(57.60, $result);
    }

    /**
     * Test that effective discount percentage is calculated correctly.
     */
    public function test_effective_discount_percentage_calculation(): void
    {
        $basePrice = 100.00;
        $discountedPrice = 57.60;
        
        // Effective discount: (1 - 57.60/100) * 100 = 42.4%
        $result = DiscountCalculator::calculateEffectiveDiscountPercentage($basePrice, $discountedPrice);
        
        $this->assertEquals(42.40, $result);
    }

    /**
     * Test that empty or null discount steps return the base price.
     */
    public function test_empty_discount_steps_return_base_price(): void
    {
        $basePrice = 150000.00;
        
        $resultEmpty = DiscountCalculator::calculate($basePrice, []);
        $resultNull = DiscountCalculator::calculate($basePrice, null);
        
        $this->assertEquals(150000.00, $resultEmpty);
        $this->assertEquals(150000.00, $resultNull);
    }
}
