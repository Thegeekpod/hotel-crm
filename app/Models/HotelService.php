<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'category',
        'price',
        'unit',
        'description',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];
}
