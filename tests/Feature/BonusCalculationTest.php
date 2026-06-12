<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Helpers\BonusCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BonusCalculationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the worked example from PRD section 5.
     * Customer threshold = 10,000,000.
     * Accumulated PAID omzet = 25,000,000.
     * Claimed = 0.
     * Available bonuses should be 2, carry over = 5,000,000.
     */
    public function test_bonus_calculation_worked_example(): void
    {
        // 1. Create a customer
        $customer = Customer::create([
            'name' => 'Test Customer',
            'bonus_threshold' => 10000000.00,
            'discount_lm' => [],
            'discount_br' => [],
        ]);

        // 2. Create paid transactions totalling 25,000,000 omzet
        // Transaction A: 15,000,000 omzet, status Lunas
        $t1 = Transaction::create([
            'nomor_bon' => 'BON-1',
            'customer_id' => $customer->id,
            'tanggal' => now()->toDateString(),
            'tanggal_pelunasan' => now()->toDateString(),
            'status' => 'Lunas',
            'is_bonus' => false,
            'ongkir' => 0.00,
        ]);
        
        TransactionItem::create([
            'transaction_id' => $t1->id,
            'product_name' => 'Test Product',
            'product_type' => 'LM',
            'harga_modal' => 50.00,
            'harga_base' => 100.00,
            'quantity' => 150000, // 15,000,000 omzet
            'discounted_unit_price' => 100.00,
            'line_omzet' => 15000000.00,
            'line_laba' => 7500000.00,
        ]);

        // Transaction B: 10,000,000 omzet, status Lunas
        $t2 = Transaction::create([
            'nomor_bon' => 'BON-2',
            'customer_id' => $customer->id,
            'tanggal' => now()->toDateString(),
            'tanggal_pelunasan' => now()->toDateString(),
            'status' => 'Lunas',
            'is_bonus' => false,
            'ongkir' => 0.00,
        ]);
        
        TransactionItem::create([
            'transaction_id' => $t2->id,
            'product_name' => 'Test Product',
            'product_type' => 'LM',
            'harga_modal' => 50.00,
            'harga_base' => 100.00,
            'quantity' => 100000, // 10,000,000 omzet
            'discounted_unit_price' => 100.00,
            'line_omzet' => 10000000.00,
            'line_laba' => 5000000.00,
        ]);

        // 3. Verify statistics
        $stats = BonusCalculator::getStats($customer);

        $this->assertEquals(25000000.00, $stats['total_paid_omzet']);
        $this->assertEquals(2, $stats['bonuses_earned']);
        $this->assertEquals(0, $stats['bonuses_claimed']);
        $this->assertEquals(2, $stats['bonuses_available']);
        $this->assertEquals(5000000.00, $stats['carry_over_omzet']);
        $this->assertEquals(50.00, $stats['progress_percentage']); // 5,000,000 / 10,000,000 = 50%
    }

    /**
     * Test cash basis accounting logic. Unpaid transactions must not count.
     */
    public function test_cash_basis_accounting_rules_for_bonus(): void
    {
        $customer = Customer::create([
            'name' => 'Test Customer',
            'bonus_threshold' => 10000000.00,
        ]);

        // Transaction: 12,000,000 omzet, but status is Piutang (unpaid)
        $t1 = Transaction::create([
            'nomor_bon' => 'BON-UNPAID',
            'customer_id' => $customer->id,
            'tanggal' => now()->toDateString(),
            'status' => 'Piutang',
            'is_bonus' => false,
            'ongkir' => 0.00,
        ]);
        
        TransactionItem::create([
            'transaction_id' => $t1->id,
            'product_name' => 'Test Product',
            'product_type' => 'LM',
            'harga_modal' => 50.00,
            'harga_base' => 100.00,
            'quantity' => 120000, // 12,000,000 omzet
            'discounted_unit_price' => 100.00,
            'line_omzet' => 12000000.00,
            'line_laba' => 6000000.00,
        ]);

        $statsBefore = BonusCalculator::getStats($customer);
        $this->assertEquals(0.00, $statsBefore['total_paid_omzet']);
        $this->assertEquals(0, $statsBefore['bonuses_available']);

        // Now mark it as Lunas (settled)
        $t1->update([
            'status' => 'Lunas',
            'tanggal_pelunasan' => now()->toDateString(),
        ]);

        $statsAfter = BonusCalculator::getStats($customer);
        $this->assertEquals(12000000.00, $statsAfter['total_paid_omzet']);
        $this->assertEquals(1, $statsAfter['bonuses_available']);
        $this->assertEquals(2000000.00, $statsAfter['carry_over_omzet']);
    }
}
