<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'harga_modal',
        'harga_base',
        'type',
    ];

    protected $casts = [
        'harga_modal' => 'decimal:2',
        'harga_base' => 'decimal:2',
    ];
}
