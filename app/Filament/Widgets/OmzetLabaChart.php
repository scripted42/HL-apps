<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class OmzetLabaChart extends ChartWidget
{
    protected static ?string $heading = 'Perbandingan Omzet & Laba Bersih (Cash Basis)';
    
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        Carbon::setLocale('id');
        $labels = [];
        $omzetData = [];
        $labaData = [];

        // Fetch data for the last 6 months, grouped by Tanggal Pelunasan (Cash Basis)
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth()->toDateString();
            $monthEnd = $date->copy()->endOfMonth()->toDateString();
            
            $labels[] = $date->translatedFormat('F Y');

            $transactions = Transaction::where('status', 'Lunas')
                ->whereBetween('tanggal_pelunasan', [$monthStart, $monthEnd])
                ->get();

            // Omzet excludes shipping & bonus items (as per D1 & AC-5.7)
            $monthlyOmzet = $transactions->where('is_bonus', false)->sum(fn ($t) => $t->omzet);
            
            // Laba includes profit from normal transactions, bonus transactions are 0 profit (as per D5)
            $monthlyLaba = $transactions->sum(fn ($t) => $t->laba);

            $omzetData[] = (float) $monthlyOmzet;
            $labaData[] = (float) $monthlyLaba;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Omzet',
                    'data' => $omzetData,
                    'borderColor' => '#6366f1', // Indigo
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Total Laba HL',
                    'data' => $labaData,
                    'borderColor' => '#10b981', // Emerald
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
