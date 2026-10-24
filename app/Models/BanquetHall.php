<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanquetHall extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'capacity_pax',
        'base_tariff',
        'amenities',
        'location',
        'status',
    ];

    protected $casts = [
        'base_tariff' => 'decimal:2',
        'capacity_pax' => 'integer',
    ];

    public function bookings()
    {
        return $this->hasMany(BanquetBooking::class);
    }
}
