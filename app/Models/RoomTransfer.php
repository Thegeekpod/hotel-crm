<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomTransfer extends Model
{
    use HasFactory;

    protected $table = 'room_transfers';

    protected $fillable = [
        'booking_id',
        'primary_booking_id',
        'from_room_id',
        'to_room_id',
        'transfer_date',
        'transfer_time',
        'old_room_rate',
        'new_room_rate',
        'nights_in_old_room',
        'remaining_nights',
        'tariff_difference',
        'transferred_by',
        'transferred_guests',
        'reason',
        'remarks',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'old_room_rate' => 'decimal:2',
        'new_room_rate' => 'decimal:2',
        'tariff_difference' => 'decimal:2',
        'nights_in_old_room' => 'integer',
        'remaining_nights' => 'integer',
        'transferred_guests' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function primaryBooking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'primary_booking_id');
    }

    public function fromRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'from_room_id');
    }

    public function toRoom(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'to_room_id');
    }

    public function staffUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }
}
