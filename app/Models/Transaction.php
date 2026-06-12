<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_bon',
        'customer_id',
        'tanggal',
        'tanggal_pelunasan',
        'status',
        'is_bonus',
        'bonuses_claimed',
        'ongkir',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_pelunasan' => 'date',
        'is_bonus' => 'boolean',
        'bonuses_claimed' => 'integer',
        'ongkir' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    // Accessors for calculated fields
    public function getOmzetAttribute(): float
    {
        return $this->items()->sum('line_omzet');
    }

    public function getLabaAttribute(): float
    {
        return $this->items()->sum('line_laba');
    }

    public function getTotalOwedAttribute(): float
    {
        return $this->omzet + $this->ongkir;
    }
}
