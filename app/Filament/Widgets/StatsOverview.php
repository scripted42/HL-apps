<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\Customer;
use App\Helpers\BonusCalculator;
use Filament\Support\Colors\Color;
use Filament\Widgets\Widget;

class StatsOverview extends Widget
{
    protected static ?int $sort = 1;

    protected static string $view = 'filament.widgets.stats-overview';

    protected function getViewData(): array
    {
        // 1. Total Piutang (Outstanding Receivables) and count
        $totalPiutang = 0.00;
        $piutangCount = 0;
        
        $piutangTransactions = Transaction::where('status', 'Piutang')->get();
        foreach ($piutangTransactions as $t) {
            $totalPiutang += (float) $t->total_owed;
            $piutangCount++;
        }

        // 2. Total Recognized Omzet (Lunas only, excluding shipping as per PRD)
        $totalOmzet = Transaction::where('status', 'Lunas')
            ->where('is_bonus', false)
            ->get()
            ->sum(fn ($t) => $t->omzet);

        // 3. Total Recognized Laba HL (Lunas only)
        $totalLaba = Transaction::where('status', 'Lunas')
            ->get()
            ->sum(fn ($t) => $t->laba);

        // 4. Customers eligible for bonuses
        $customersWithBonusCount = 0;
        $customers = Customer::all();
        foreach ($customers as $customer) {
            $stats = BonusCalculator::getStats($customer);
            if ($stats['bonuses_available'] > 0) {
                $customersWithBonusCount++;
            }
        }

        // 5. LM and BR specific values for Omzet and Laba
        $omzetLM = 0.00;
        $omzetBR = 0.00;
        $labaLM = 0.00;
        $labaBR = 0.00;

        $paidTransactions = Transaction::where('status', 'Lunas')->with('items')->get();
        foreach ($paidTransactions as $t) {
            foreach ($t->items as $item) {
                if ($item->product_type === 'LM') {
                    $omzetLM += (float) $item->line_omzet;
                    $labaLM += (float) $item->line_laba;
                } else {
                    $omzetBR += (float) $item->line_omzet;
                    $labaBR += (float) $item->line_laba;
                }
            }
        }

        return [
            'totalPiutang' => $totalPiutang,
            'piutangCount' => $piutangCount,
            'totalOmzet' => $totalOmzet,
            'totalLaba' => $totalLaba,
            'customersWithBonusCount' => $customersWithBonusCount,
            'omzetLM' => $omzetLM,
            'omzetBR' => $omzetBR,
            'labaLM' => $labaLM,
            'labaBR' => $labaBR,
        ];
    }
}
