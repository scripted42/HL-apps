<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Helpers\DiscountCalculator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed the default Admin User for Filament
        $admin = User::create([
            'name' => 'Admin HL',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'has_completed_tour' => false,
        ]);

        // 2. Seed Mock Products (LM & BR Types)
        $p1 = Product::create([
            'name' => 'Besi Beton 10mm (BR)',
            'harga_modal' => 55000.00,
            'harga_base' => 80000.00,
            'type' => 'BR',
        ]);

        $p2 = Product::create([
            'name' => 'Baja WF 150 (BR)',
            'harga_modal' => 1250000.00,
            'harga_base' => 1800000.00,
            'type' => 'BR',
        ]);

        $p3 = Product::create([
            'name' => 'Semen Padang 50kg (LM)',
            'harga_modal' => 48000.00,
            'harga_base' => 70000.00,
            'type' => 'LM',
        ]);

        $p4 = Product::create([
            'name' => 'Pipa PVC 3 Inch (LM)',
            'harga_modal' => 95000.00,
            'harga_base' => 150000.00,
            'type' => 'LM',
        ]);

        // 3. Seed Mock Customers with Cascading Discounts
        $c1 = Customer::create([
            'name' => 'CV. Jaya Abadi (Anto)',
            'discount_lm' => [20, 20, 10], // Effective: 42.4%
            'discount_br' => [15, 10],     // Effective: 23.5%
            'bonus_threshold' => 10000000.00, // 10jt
        ]);

        $c2 = Customer::create([
            'name' => 'Budi Contractors',
            'discount_lm' => [10, 10],     // Effective: 19%
            'discount_br' => [5],          // Effective: 5%
            'bonus_threshold' => 15000000.00, // 15jt
        ]);

        $c3 = Customer::create([
            'name' => 'Toko Citra Bangunan',
            'discount_lm' => [25, 20, 15], // Effective: 49%
            'discount_br' => [20, 10, 5],  // Effective: 31.6%
            'bonus_threshold' => 8000000.00,  // 8jt
        ]);

        // 4. Seed Historical Transactions
        
        // Transaction 1: Customer 1, Lunas, Non-Bonus, Paid 3 months ago
        $t1 = Transaction::create([
            'nomor_bon' => 'BON-202603-001',
            'customer_id' => $c1->id,
            'tanggal' => Carbon::now()->subMonths(3)->toDateString(),
            'tanggal_pelunasan' => Carbon::now()->subMonths(3)->addDays(5)->toDateString(),
            'status' => 'Lunas',
            'is_bonus' => false,
            'bonuses_claimed' => 0,
            'ongkir' => 150000.00,
            'deskripsi' => 'Pengiriman proyek Ruko Kelapa Gading',
        ]);

        // Add items for Transaction 1
        $t1_item1_base = $p1->harga_base;
        $t1_item1_disc = DiscountCalculator::calculate($t1_item1_base, $c1->discount_br);
        TransactionItem::create([
            'transaction_id' => $t1->id,
            'product_id' => $p1->id,
            'product_name' => $p1->name,
            'product_type' => $p1->type,
            'harga_modal' => $p1->harga_modal,
            'harga_base' => $t1_item1_base,
            'discount_steps' => $c1->discount_br,
            'quantity' => 100, // 100 * 61,200 = 6,120,000 omzet
            'discounted_unit_price' => $t1_item1_disc,
            'line_omzet' => $t1_item1_disc * 100,
            'line_laba' => ($t1_item1_disc - $p1->harga_modal) * 100,
        ]);

        $t1_item2_base = $p3->harga_base;
        $t1_item2_disc = DiscountCalculator::calculate($t1_item2_base, $c1->discount_lm);
        TransactionItem::create([
            'transaction_id' => $t1->id,
            'product_id' => $p3->id,
            'product_name' => $p3->name,
            'product_type' => $p3->type,
            'harga_modal' => $p3->harga_modal,
            'harga_base' => $t1_item2_base,
            'discount_steps' => $c1->discount_lm,
            'quantity' => 100, // 100 * 40,320 = 4,032,000 omzet
            'discounted_unit_price' => $t1_item2_disc,
            'line_omzet' => $t1_item2_disc * 100,
            'line_laba' => ($t1_item2_disc - $p3->harga_modal) * 100,
        ]);
        // Total Omzet T1 = 6,120,000 + 4,032,000 = 10,152,000. Since status = Lunas, this counts towards bonus threshold (10jt threshold)! So 1 bonus earned.

        // Transaction 2: Customer 1, Piutang (Outstanding), Non-Bonus, Created 1 week ago
        $t2 = Transaction::create([
            'nomor_bon' => 'BON-202606-001',
            'customer_id' => $c1->id,
            'tanggal' => Carbon::now()->subDays(7)->toDateString(),
            'tanggal_pelunasan' => null,
            'status' => 'Piutang',
            'is_bonus' => false,
            'bonuses_claimed' => 0,
            'ongkir' => 75000.00,
            'deskripsi' => 'Pengiriman barang tambahan fitting pipa',
        ]);

        $t2_item_base = $p4->harga_base;
        $t2_item_disc = DiscountCalculator::calculate($t2_item_base, $c1->discount_lm);
        TransactionItem::create([
            'transaction_id' => $t2->id,
            'product_id' => $p4->id,
            'product_name' => $p4->name,
            'product_type' => $p4->type,
            'harga_modal' => $p4->harga_modal,
            'harga_base' => $t2_item_base,
            'discount_steps' => $c1->discount_lm,
            'quantity' => 20, // 20 * 86,400 = 1,728,000 omzet (Piutang)
            'discounted_unit_price' => $t2_item_disc,
            'line_omzet' => $t2_item_disc * 20,
            'line_laba' => ($t2_item_disc - $p4->harga_modal) * 20,
        ]);

        // Transaction 3: Customer 2, Lunas, Non-Bonus, Large order (30jt omzet -> 2 bonuses earned since threshold is 15jt)
        $t3 = Transaction::create([
            'nomor_bon' => 'BON-202605-001',
            'customer_id' => $c2->id,
            'tanggal' => Carbon::now()->subMonths(1)->toDateString(),
            'tanggal_pelunasan' => Carbon::now()->subMonths(1)->addDays(10)->toDateString(),
            'status' => 'Lunas',
            'is_bonus' => false,
            'bonuses_claimed' => 0,
            'ongkir' => 300000.00,
            'deskripsi' => 'Bahan pondasi utama ruko 3 lantai',
        ]);

        $t3_item_base = $p2->harga_base;
        $t3_item_disc = DiscountCalculator::calculate($t3_item_base, $c2->discount_br);
        TransactionItem::create([
            'transaction_id' => $t3->id,
            'product_id' => $p2->id,
            'product_name' => $p2->name,
            'product_type' => $p2->type,
            'harga_modal' => $p2->harga_modal,
            'harga_base' => $t3_item_base,
            'discount_steps' => $c2->discount_br,
            'quantity' => 18, // 18 * 1,710,000 = 30,780,000 omzet (Lunas)
            'discounted_unit_price' => $t3_item_disc,
            'line_omzet' => $t3_item_disc * 18,
            'line_laba' => ($t3_item_disc - $p2->harga_modal) * 18,
        ]);

        // Transaction 4: Customer 2, Lunas, Bonus Transaction (Claims 1 bonus, so 1 bonus remains available)
        $t4 = Transaction::create([
            'nomor_bon' => 'BON-202605-B01',
            'customer_id' => $c2->id,
            'tanggal' => Carbon::now()->subDays(5)->toDateString(),
            'tanggal_pelunasan' => Carbon::now()->subDays(5)->toDateString(),
            'status' => 'Lunas',
            'is_bonus' => true,
            'bonuses_claimed' => 1,
            'ongkir' => 0.00,
            'deskripsi' => 'Pengambilan jatah bonus Besi Beton',
        ]);

        // Bonus items are free: discounted_unit_price = 0, line_omzet = 0, line_laba = 0
        TransactionItem::create([
            'transaction_id' => $t4->id,
            'product_id' => $p1->id,
            'product_name' => $p1->name,
            'product_type' => $p1->type,
            'harga_modal' => $p1->harga_modal,
            'harga_base' => $p1->harga_base,
            'discount_steps' => null,
            'quantity' => 50,
            'discounted_unit_price' => 0.00,
            'line_omzet' => 0.00,
            'line_laba' => 0.00,
        ]);

        // Transaction 5: Customer 3, Lunas, Non-Bonus, Under threshold (5.7jt omzet -> threshold is 8jt, so 0 bonuses available)
        $t5 = Transaction::create([
            'nomor_bon' => 'BON-202605-002',
            'customer_id' => $c3->id,
            'tanggal' => Carbon::now()->subDays(20)->toDateString(),
            'tanggal_pelunasan' => Carbon::now()->subDays(18)->toDateString(),
            'status' => 'Lunas',
            'is_bonus' => false,
            'bonuses_claimed' => 0,
            'ongkir' => 50000.00,
            'deskripsi' => 'Pengiriman pesanan pipa proyek rumah hunian',
        ]);

        $t5_item_base = $p4->harga_base;
        $t5_item_disc = DiscountCalculator::calculate($t5_item_base, $c3->discount_lm);
        TransactionItem::create([
            'transaction_id' => $t5->id,
            'product_id' => $p4->id,
            'product_name' => $p4->name,
            'product_type' => $p4->type,
            'harga_modal' => $p4->harga_modal,
            'harga_base' => $t5_item_base,
            'discount_steps' => $c3->discount_lm,
            'quantity' => 75, // 75 * 76,500 = 5,737,500 omzet (Lunas)
            'discounted_unit_price' => $t5_item_disc,
            'line_omzet' => $t5_item_disc * 75,
            'line_laba' => ($t5_item_disc - $p4->harga_modal) * 75,
        ]);
    }
}
