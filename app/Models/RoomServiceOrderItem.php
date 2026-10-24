<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomServiceOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_service_order_id',
        'hotel_service_id',
        'item_name',
        'unit_price',
        'quantity',
        'line_total',
        'instructions',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'line_total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(RoomServiceOrder::class, 'room_service_order_id');
    }

    public function service()
    {
        return $this->belongsTo(HotelService::class, 'hotel_service_id');
    }
}
