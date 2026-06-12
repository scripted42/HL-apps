<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_name',
        'product_type',
        'harga_modal',
        'harga_base',
        'discount_steps',
        'quantity',
        'discounted_unit_price',
        'line_omzet',
        'line_laba',
    ];

    protected $casts = [
        'discount_steps' => 'array',
        'harga_modal' => 'decimal:2',
        'harga_base' => 'decimal:2',
        'discounted_unit_price' => 'decimal:2',
        'line_omzet' => 'decimal:2',
        'line_laba' => 'decimal:2',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
