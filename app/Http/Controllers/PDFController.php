<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PDFController extends Controller
{
    /**
     * Generate and download PDF receipt for a single transaction (Bon).
     *
     * @param Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function downloadReceipt(Transaction $transaction)
    {
        // Load relationships
        $transaction->load(['customer', 'items']);

        // Generate PDF
        $pdf = Pdf::loadView('pdf.receipt', [
            'transaction' => $transaction,
        ]);

        $filename = 'Receipt_' . str_replace('-', '_', $transaction->nomor_bon) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate and download PDF monthly recap for a customer.
     *
     * @param Customer $customer
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function downloadCustomerRecap(Customer $customer, Request $request)
    {
        $selectedMonth = $request->query('month', now()->format('Y-m'));

        $year = Carbon::parse($selectedMonth . '-01')->year;
        $month = Carbon::parse($selectedMonth . '-01')->month;

        $transactions = Transaction::where('customer_id', $customer->id)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->with(['items'])
            ->orderBy('tanggal', 'asc')
            ->get();

        // Calculate statistics
        $totalPiutang = 0.00;
        $totalPaid = 0.00;
        $omzetLM = 0.00;
        $omzetBR = 0.00;
        $labaLM = 0.00;
        $labaBR = 0.00;

        foreach ($transactions as $t) {
            $owed = (float) $t->total_owed;
            
            if ($t->status === 'Piutang') {
                $totalPiutang += $owed;
            } else {
                $totalPaid += $owed;
                
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
        }

        Carbon::setLocale('id');
        $monthName = Carbon::parse($selectedMonth . '-01')->translatedFormat('F Y');

        $pdf = Pdf::loadView('pdf.recap', [
            'customer' => $customer,
            'transactions' => $transactions,
            'selectedMonth' => $selectedMonth,
            'monthName' => $monthName,
            'totalPiutang' => $totalPiutang,
            'totalPaid' => $totalPaid,
            'omzetLM' => $omzetLM,
            'omzetBR' => $omzetBR,
            'labaLM' => $labaLM,
            'labaBR' => $labaBR,
        ]);

        $filename = 'Rekap_' . str_replace(' ', '_', $customer->name) . '_' . str_replace('-', '_', $selectedMonth) . '.pdf';

        return $pdf->download($filename);
    }
}
