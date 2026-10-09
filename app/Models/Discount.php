<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $table = 'discounts';

    protected $fillable = [
        'discount_percentage',
        'status',
    ];

    protected $casts = [
        'discount_percentage' => 'float',
    ];
}
