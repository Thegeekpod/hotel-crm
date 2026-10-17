<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Settlement extends Model
{
    use HasFactory;

    protected $table = 'settlements';

    protected $fillable = [
        'settlement_number',
        'folio_number',
        'guest_id',
        'booking_id',
        'payment_mode_id',
        'total_room_charges',
        'total_extra_charges',
        'discount_amount',
        'tax_amount',
        'payable_amount',
        'advance_adjusted',
        'paid_amount',
        'refund_amount',
        'balance_amount',
        'settlement_date',
        'settlement_time',
        'status',
        'remarks',
    ];

    protected $casts = [
        'settlement_date' => 'date',
        'total_room_charges' => 'decimal:2',
        'total_extra_charges' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'payable_amount' => 'decimal:2',
        'advance_adjusted' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
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

    public function paymentMode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class, 'payment_mode_id');
    }

    public function checkouts(): HasMany
    {
        return $this->hasMany(Checkout::class, 'settlement_id');
    }
}
