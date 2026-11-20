<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomServiceOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'room_id',
        'booking_id',
        'guest_name',
        'room_number',
        'table_number',
        'customer_gstin',
        'subtotal',
        'discount_type',
        'discount_percentage',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'payment_status',
        'order_status',
        'delivery_time',
        'special_instructions',
        'served_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function items()
    {
        return $this->hasMany(RoomServiceOrderItem::class);
    }
}
