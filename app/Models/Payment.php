<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'guest_id',
        'booking_id',
        'payment_mode_id',
        'amount',
        'advance_amount',
        'balance',
        'folio_number',
        'payment_remarks',
        'payment_date',
        'payment_time',
        'payment_type',
        'transaction_id',
        'status',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'advance_amount' => 'decimal:2',
        'balance' => 'decimal:2',
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
}
