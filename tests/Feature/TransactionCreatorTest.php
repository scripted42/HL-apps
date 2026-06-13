<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Transaction;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Livewire\TransactionCreator;

class TransactionCreatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_transaction_creator_lifecycle()
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $customer = Customer::create([
            'name' => 'Budi Contractors',
            'bonus_threshold' => 10000000.00,
            'discount_lm' => [20, 20, 10],
            'discount_br' => [20, 10],
        ]);

        $product = Product::create([
            'name' => 'Baja WF 150 (BR)',
            'type' => 'BR',
            'harga_modal' => 80.00,
            'harga_base' => 100.00,
        ]);

        // Test Livewire component
        $component = Livewire::actingAs($user)
            ->test(TransactionCreator::class)
            ->set('customer_id', $customer->id)
            ->set('items.0.product_id', $product->id);

        // Let's assert that the product details are auto-populated
        $component->assertSet('items.0.product_name', 'Baja WF 150 (BR)');
        $component->assertSet('items.0.product_type', 'BR');
        $component->assertSet('items.0.harga_base', 100.00);

        // Test adding an item
        $component->call('addItem');
        $component->assertCount('items', 2);

        // Test saving
        $component->set('items.1.product_id', $product->id)
            ->set('items.0.quantity', 10)
            ->set('items.1.quantity', 5)
            ->call('save')
            ->assertRedirect(route('transactions.index'));

        // Assert transaction was saved to database
        $this->assertDatabaseHas('transactions', [
            'customer_id' => $customer->id,
            'status' => 'Piutang',
        ]);
    }
}
