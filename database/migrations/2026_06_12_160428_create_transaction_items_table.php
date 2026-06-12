<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('set null');
            
            // Historical Snapshots (Critical for data integrity when prices, products, or customer discounts change)
            $table->string('product_name');
            $table->enum('product_type', ['LM', 'BR']);
            $table->decimal('harga_modal', 15, 2);
            $table->decimal('harga_base', 15, 2);
            $table->json('discount_steps')->nullable(); // array of discount steps at the time (e.g. [20, 20, 10])
            
            $table->integer('quantity');
            $table->decimal('discounted_unit_price', 15, 2);
            $table->decimal('line_omzet', 15, 2);
            $table->decimal('line_laba', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
