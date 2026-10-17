<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkout extends Model
{
    use HasFactory;

    protected $table = 'checkouts';

    protected $fillable = [
        'guest_id',
        'booking_id',
        'room_id',
        'settlement_id',
        'checkout_date',
        'checkout_time',
        'actual_checkout_at',
        'total_nights',
        'room_charges',
        'extra_charges',
        'late_checkout_charge',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'paid_amount',
        'balance_amount',
        'status',
        'remarks',
    ];

    protected $casts = [
        'checkout_date' => 'date',
        'actual_checkout_at' => 'datetime',
        'total_nights' => 'integer',
        'room_charges' => 'decimal:2',
        'extra_charges' => 'decimal:2',
        'late_checkout_charge' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_amount' => 'decimal:2',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(Settlement::class, 'settlement_id');
    }
}
