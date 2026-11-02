<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodAdvance extends Model
{
    use HasFactory;

    protected $table = 'food_advances';

    protected $fillable = [
        'booking_id',
        'advance_amount',
    ];

    protected $casts = [
        'advance_amount' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}

