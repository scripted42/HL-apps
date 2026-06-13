<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Transaction;
use App\Helpers\BonusCalculator;
use Carbon\Carbon;

class CustomerDetail extends Component
{
    public Customer $customer;
    public $selectedMonthToSettle = null; // YYYY-MM
    public $selectedMonthName = '';

    public function mount(Customer $customer)
    {
        $this->customer = $customer;
    }

    public function openSettleModal($month)
    {
        $this->selectedMonthToSettle = $month;
        $this->selectedMonthName = Carbon::parse($month . '-01')->translatedFormat('F Y');
        $this->dispatch('show-settle-modal');
    }

    public function settleMonth()
    {
        if (!$this->selectedMonthToSettle) return;

        $year = Carbon::parse($this->selectedMonthToSettle . '-01')->year;
        $month = Carbon::parse($this->selectedMonthToSettle . '-01')->month;

        $unpaidTransactions = Transaction::where('customer_id', $this->customer->id)
            ->where('status', 'Piutang')
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->get();

        foreach ($unpaidTransactions as $t) {
            $t->update([
                'status' => 'Lunas',
                'tanggal_pelunasan' => now(),
            ]);
        }

        $this->dispatch('hide-settle-modal');
        session()->flash('message', "Semua tagihan untuk bulan {$this->selectedMonthName} berhasil dilunasi.");
        
        $this->selectedMonthToSettle = null;
        $this->selectedMonthName = '';
    }

    public function settleSingleTransaction($id)
    {
        $t = Transaction::findOrFail($id);
        $t->update([
            'status' => 'Lunas',
            'tanggal_pelunasan' => now(),
        ]);
        session()->flash('message', "Bon #{$t->nomor_bon} berhasil dilunasi.");
    }

    public function render()
    {
        Carbon::setLocale('id');

        // Fetch all transactions for this customer
        $allTransactions = Transaction::where('customer_id', $this->customer->id)
            ->with(['items'])
            ->orderBy('tanggal', 'desc')
            ->get();

        // 1. Overall customer stats
        $totalPiutang = 0.00;
        $totalPaid = 0.00;
        foreach ($allTransactions as $t) {
            if ($t->status === 'Piutang') {
                $totalPiutang += (float) $t->total_owed;
            } else {
                $totalPaid += (float) $t->total_owed;
            }
        }

        $bonusStats = BonusCalculator::getStats($this->customer);

        // 2. Group transactions by Month-Year (YYYY-MM)
        $groupedTransactions = [];
        foreach ($allTransactions as $t) {
            $monthKey = $t->tanggal->format('Y-m');
            if (!isset($groupedTransactions[$monthKey])) {
                $groupedTransactions[$monthKey] = [
                    'month_name' => $t->tanggal->translatedFormat('F Y'),
                    'month_key' => $monthKey,
                    'transactions' => [],
                    'stats' => [
                        'piutang' => 0.00,
                        'paid' => 0.00,
                        'omzet_lm' => 0.00,
                        'omzet_br' => 0.00,
                        'laba_lm' => 0.00,
                        'laba_br' => 0.00,
                    ]
                ];
            }

            $groupedTransactions[$monthKey]['transactions'][] = $t;
            $owed = (float) $t->total_owed;

            if ($t->status === 'Piutang') {
                $groupedTransactions[$monthKey]['stats']['piutang'] += $owed;
            } else {
                $groupedTransactions[$monthKey]['stats']['paid'] += $owed;

                // Breakdown omzet/laba
                foreach ($t->items as $item) {
                    if ($item->product_type === 'LM') {
                        $groupedTransactions[$monthKey]['stats']['omzet_lm'] += (float) $item->line_omzet;
                        $groupedTransactions[$monthKey]['stats']['laba_lm'] += (float) $item->line_laba;
                    } else {
                        $groupedTransactions[$monthKey]['stats']['omzet_br'] += (float) $item->line_omzet;
                        $groupedTransactions[$monthKey]['stats']['laba_br'] += (float) $item->line_laba;
                    }
                }
            }
        }

        return view('livewire.customer-detail', [
            'totalPiutang' => $totalPiutang,
            'totalPaid' => $totalPaid,
            'bonusStats' => $bonusStats,
            'groupedTransactions' => $groupedTransactions,
        ]);
    }
}
