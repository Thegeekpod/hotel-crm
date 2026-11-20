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
        'is_kot',
        'kot_number',
        'kot_sequence',
        'table_number',
        'table_id',
        'room_id',
        'booking_id',
        'customer_name',
        'customer_phone',
        'customer_gstin',
        'room_number',
        'subtotal',
        'discount_type',
        'discount_percentage',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'total_amount',
        'billing_type',
        'payment_status',
        'status',
        'server_name',
        'special_instructions',
        'room_service_order_id',
        'parent_order_id',
    ];

    protected $casts = [
        'is_kot' => 'boolean',
        'kot_sequence' => 'integer',
        'subtotal' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
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

    public function tableManage()
    {
        return $this->belongsTo(TableManage::class, 'table_id');
    }

    public function parentOrder()
    {
        return $this->belongsTo(PosOrder::class, 'parent_order_id');
    }

    public function kotOrders()
    {
        return $this->hasMany(PosOrder::class, 'parent_order_id');
    }

    /**
     * Scope for KOT tickets only.
     */
    public function scopeKot($query)
    {
        return $query->where('is_kot', true);
    }

    /**
     * Scope for Master Bills only.
     */
    public function scopeBills($query)
    {
        return $query->where('is_kot', false);
    }

    /**
     * Scope for active pending KOTs for a table.
     */
    public function scopeActiveTableKots($query, $tableId)
    {
        return $query->where('table_id', $tableId)
            ->where('is_kot', true)
            ->whereNull('parent_order_id')
            ->where('status', '!=', 'Cancelled')
            ->where('payment_status', 'Pending');
    }
}
