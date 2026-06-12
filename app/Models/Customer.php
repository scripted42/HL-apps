<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'discount_lm',
        'discount_br',
        'bonus_threshold',
    ];

    protected $casts = [
        'discount_lm' => 'array',
        'discount_br' => 'array',
        'bonus_threshold' => 'decimal:2',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
