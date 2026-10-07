<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomMaintenance extends Model
{
    use HasFactory;

    protected $table = 'room_maintenances';

    protected $fillable = [
        'room_id',
        'reason',
        'assign',
        'expected_date_time',
        'note',
        'status',
    ];

    protected $casts = [
        'expected_date_time' => 'datetime',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}
