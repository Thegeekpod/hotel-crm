<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomHousekeepingHistory extends Model
{
    use HasFactory;

    protected $table = 'room_housekeeping_histories';

    protected $fillable = [
        'room_id',
        'housekeeping_status_id',
        'start_time',
        'completion_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'completion_time' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function housekeepingStatus(): BelongsTo
    {
        return $this->belongsTo(HousekeepingState::class, 'housekeeping_status_id');
    }
}
