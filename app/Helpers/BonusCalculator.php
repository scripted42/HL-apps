<?php

namespace App\Helpers;

use App\Models\Customer;
use App\Models\Transaction;

class BonusCalculator
{
    /**
     * Calculate all bonus-related metrics for a customer.
     *
     * @param Customer $customer
     * @return array
     */
    public static function getStats(Customer $customer): array
    {
        $threshold = (float) $customer->bonus_threshold;
        if ($threshold <= 0) {
            $threshold = 10000000.00; // Default fallback to 10jt
        }

        // 1. Total paid omzet: sum of line_omzet of Lunas transactions where is_bonus = false
        // Using Eloquent relations is cleaner
        $totalPaidOmzet = (float) Transaction::where('customer_id', $customer->id)
            ->where('status', 'Lunas')
            ->where('is_bonus', false)
            ->get()
            ->sum(function ($transaction) {
                return $transaction->omzet; // Uses the accessor
            });

        // 2. Total bonuses earned
        $bonusesEarned = (int) floor($totalPaidOmzet / $threshold);

        // 3. Total bonuses claimed: sum of bonuses_claimed from is_bonus = true transactions
        // We include all created bonus transactions to prevent users from double spending before the transaction is set to Lunas
        $bonusesClaimed = (int) Transaction::where('customer_id', $customer->id)
            ->where('is_bonus', true)
            ->sum('bonuses_claimed');

        // 4. Bonuses available
        $bonusesAvailable = max(0, $bonusesEarned - $bonusesClaimed);

        // 5. Carry over / remainder omzet
        // Remaining paid omzet that counts towards the next bonus
        $carryOverOmzet = fmod($totalPaidOmzet, $threshold);

        // 6. Progress to next bonus
        $progressPercentage = ($threshold > 0) ? min(100.00, ($carryOverOmzet / $threshold) * 100) : 0.00;

        return [
            'total_paid_omzet' => $totalPaidOmzet,
            'bonuses_earned' => $bonusesEarned,
            'bonuses_claimed' => $bonusesClaimed,
            'bonuses_available' => $bonusesAvailable,
            'carry_over_omzet' => $carryOverOmzet,
            'accumulated_omzet' => $carryOverOmzet,
            'remaining_for_next_bonus' => max(0.00, $threshold - $carryOverOmzet),
            'progress_percentage' => round($progressPercentage, 2),
            'threshold' => $threshold,
        ];
    }
}
