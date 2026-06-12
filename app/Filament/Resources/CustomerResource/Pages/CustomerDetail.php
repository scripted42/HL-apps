<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Transaction;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class CustomerDetail extends Page
{
    use InteractsWithRecord;

    protected static string $resource = CustomerResource::class;

    protected static string $view = 'filament.resources.customer-resource.pages.customer-detail';

    public ?string $selectedMonth = null;
    public ?string $paymentDate = null; // Used for settlement modal
    
    // Properties for statistics
    public float $totalPiutang = 0.00;
    public float $totalPaid = 0.00;
    public float $omzetLM = 0.00;
    public float $omzetBR = 0.00;
    public float $labaLM = 0.00;
    public float $labaBR = 0.00;
    public int $piutangCount = 0;
    public int $paidCount = 0;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->paymentDate = now()->toDateString();
        
        // Find the latest transaction month, otherwise default to current month
        $latestTransaction = Transaction::where('customer_id', $this->record->id)
            ->orderBy('tanggal', 'desc')
            ->first();

        if ($latestTransaction) {
            $this->selectedMonth = Carbon::parse($latestTransaction->tanggal)->format('Y-m');
        } else {
            $this->selectedMonth = now()->format('Y-m');
        }

        $this->calculateStats();
    }

    public function updatedSelectedMonth(): void
    {
        $this->calculateStats();
    }

    /**
     * Get list of months that have transactions for this customer.
     */
    public function getMonths(): array
    {
        $dates = Transaction::where('customer_id', $this->record->id)
            ->orderBy('tanggal', 'desc')
            ->pluck('tanggal')
            ->map(fn($date) => Carbon::parse($date)->format('Y-m'))
            ->unique()
            ->toArray();

        // Ensure current month is at least in options
        $currentMonth = now()->format('Y-m');
        if (!in_array($currentMonth, $dates)) {
            $dates[] = $currentMonth;
        }

        // Format dates into readable localized Indonesian months
        $options = [];
        Carbon::setLocale('id');
        foreach ($dates as $date) {
            $carbonDate = Carbon::createFromFormat('Y-m', $date);
            $options[$date] = $carbonDate->translatedFormat('F Y');
        }

        return $options;
    }

    /**
     * Get transactions for the selected month.
     */
    public function getTransactions()
    {
        if (!$this->selectedMonth) {
            return collect();
        }

        $year = Carbon::parse($this->selectedMonth . '-01')->year;
        $month = Carbon::parse($this->selectedMonth . '-01')->month;

        return Transaction::where('customer_id', $this->record->id)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->with(['items'])
            ->orderBy('tanggal', 'desc')
            ->get();
    }

    /**
     * Calculate monthly statistics based on selected month.
     */
    public function calculateStats(): void
    {
        $transactions = $this->getTransactions();

        $this->totalPiutang = 0.00;
        $this->totalPaid = 0.00;
        $this->omzetLM = 0.00;
        $this->omzetBR = 0.00;
        $this->labaLM = 0.00;
        $this->labaBR = 0.00;
        $this->piutangCount = 0;
        $this->paidCount = 0;

        foreach ($transactions as $transaction) {
            $owed = (float) $transaction->total_owed;
            
            if ($transaction->status === 'Piutang') {
                $this->totalPiutang += $owed;
                $this->piutangCount++;
            } else {
                $this->totalPaid += $owed;
                $this->paidCount++;
                
                // Add up OMZET & LABA broken down by LM and BR from line items
                foreach ($transaction->items as $item) {
                    if ($item->product_type === 'LM') {
                        $this->omzetLM += (float) $item->line_omzet;
                        $this->labaLM += (float) $item->line_laba;
                    } else {
                        $this->omzetBR += (float) $item->line_omzet;
                        $this->labaBR += (float) $item->line_laba;
                    }
                }
            }
        }
    }

    /**
     * Settle all outstanding transactions in the selected month.
     */
    public function settleMonth(): void
    {
        if (!$this->selectedMonth || !$this->paymentDate) {
            return;
        }

        $year = Carbon::parse($this->selectedMonth . '-01')->year;
        $month = Carbon::parse($this->selectedMonth . '-01')->month;

        $updatedCount = Transaction::where('customer_id', $this->record->id)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->where('status', 'Piutang')
            ->update([
                'status' => 'Lunas',
                'tanggal_pelunasan' => $this->paymentDate,
            ]);

        $this->calculateStats();

        if ($updatedCount > 0) {
            Notification::make()
                ->title('Bulan Berhasil Dilunasi')
                ->body("{$updatedCount} transaksi pada bulan ini telah diubah statusnya menjadi Lunas.")
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Informasi')
                ->body('Tidak ada transaksi piutang yang perlu dilunasi pada bulan ini.')
                ->info()
                ->send();
        }
    }

    /**
     * Settle a single outstanding transaction.
     */
    public function settleSingleTransaction($id, $paymentDate): void
    {
        $transaction = Transaction::where('customer_id', $this->record->id)
            ->where('id', $id)
            ->where('status', 'Piutang')
            ->first();

        if ($transaction) {
            $transaction->update([
                'status' => 'Lunas',
                'tanggal_pelunasan' => $paymentDate ?: now()->toDateString(),
            ]);

            $this->calculateStats();

            Notification::make()
                ->title('Transaksi Dilunasi')
                ->body("Bon {$transaction->nomor_bon} berhasil dilunasi.")
                ->success()
                ->send();
        }
    }
}
