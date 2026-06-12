<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that deleting a customer or product does not break existing historical transaction items.
     */
    public function test_soft_deletes_preserve_historical_data_integrity(): void
    {
        // 1. Create product, customer, and transaction
        $customer = Customer::create([
            'name' => 'Legacy Customer',
            'bonus_threshold' => 10000000.00,
        ]);

        $product = Product::create([
            'name' => 'Legacy Product',
            'harga_modal' => 50000.00,
            'harga_base' => 100000.00,
            'type' => 'LM',
        ]);

        $transaction = Transaction::create([
            'nomor_bon' => 'BON-LEGACY',
            'customer_id' => $customer->id,
            'tanggal' => now()->toDateString(),
            'status' => 'Lunas',
            'tanggal_pelunasan' => now()->toDateString(),
        ]);

        $item = TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'product_name' => $product->name, // snapshot
            'product_type' => $product->type, // snapshot
            'harga_modal' => $product->harga_modal, // snapshot
            'harga_base' => $product->harga_base, // snapshot
            'quantity' => 10,
            'discounted_unit_price' => 100000.00,
            'line_omzet' => 1000000.00,
            'line_laba' => 500000.00,
        ]);

        // Verify initial state
        $this->assertEquals('Legacy Product', $item->product_name);
        $this->assertEquals('Legacy Customer', $transaction->customer->name);

        // 2. Soft-delete the customer and the product
        $customer->delete();
        $product->delete();

        // 3. Re-fetch transaction and items from DB
        $freshItem = TransactionItem::find($item->id);
        $freshTransaction = Transaction::find($transaction->id);

        // Assert that relationships are handled gracefully
        $this->assertNull($freshItem->product); // Product relation is null since it's soft-deleted
        $this->assertEquals('Legacy Product', $freshItem->product_name); // BUT the snapshot name is preserved!
        $this->assertEquals(50000.00, $freshItem->harga_modal); // Snapshot cost price is preserved!
        $this->assertEquals(100000.00, $freshItem->harga_base); // Snapshot base price is preserved!

        // Customer relation remains valid under Laravel SoftDeletes if we include trashed,
        // or we check the database constraints
        $this->assertNull($freshTransaction->customer); // default query excludes soft-deleted customer
        
        // Re-fetching with trashed includes the customer
        $trashedCustomer = Customer::withTrashed()->find($freshTransaction->customer_id);
        $this->assertNotNull($trashedCustomer);
        $this->assertEquals('Legacy Customer', $trashedCustomer->name);
    }
}
