<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BanquetBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'banquet_hall_id',
        'booking_id',
        'client_name',
        'company_name',
        'phone',
        'email',
        'event_name',
        'event_date',
        'time_slot',
        'guaranteed_pax',
        'hall_rental',
        'catering_per_plate',
        'total_amount',
        'advance_paid',
        'balance_due',
        'status',
        'function_sheet_notes',
    ];

    protected $casts = [
        'event_date' => 'date',
        'guaranteed_pax' => 'integer',
        'hall_rental' => 'decimal:2',
        'catering_per_plate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'advance_paid' => 'decimal:2',
        'balance_due' => 'decimal:2',
    ];

    public function hall()
    {
        return $this->belongsTo(BanquetHall::class, 'banquet_hall_id');
    }

    public function roomBooking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
