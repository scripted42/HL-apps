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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bon')->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->date('tanggal');
            $table->date('tanggal_pelunasan')->nullable();
            $table->enum('status', ['Piutang', 'Lunas'])->default('Piutang');
            $table->boolean('is_bonus')->default(false);
            $table->integer('bonuses_claimed')->default(0); // number of bonuses used in this transaction
            $table->decimal('ongkir', 15, 2)->default(0.00);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
