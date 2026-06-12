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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('discount_lm')->nullable(); // stored as array of percentages, e.g. [20, 20, 10]
            $table->json('discount_br')->nullable(); // stored as array of percentages, e.g. [20, 10]
            $table->decimal('bonus_threshold', 15, 2)->default(10000000.00); // Default: Rp 10.000.000
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
