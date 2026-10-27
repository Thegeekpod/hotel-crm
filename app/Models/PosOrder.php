<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'order_type',
        'table_number',
        'room_id',
        'booking_id',
        'customer_name',
        'room_number',
        'subtotal',
        'tax_percentage',
        'tax_amount',
        'total_amount',
        'billing_type',
        'payment_status',
        'status',
        'server_name',
        'special_instructions',
        'room_service_order_id',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function items()
    {
        return $this->hasMany(PosOrderItem::class, 'pos_order_id');
    }

    public function roomServiceOrder()
    {
        return $this->belongsTo(RoomServiceOrder::class, 'room_service_order_id');
    }
}
