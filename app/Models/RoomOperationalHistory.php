<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomOperationalHistory extends Model
{
    use HasFactory;

    protected $table = 'room_operational_histories';

    protected $fillable = [
        'room_id',
        'operational_status_id',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function operationalStatus(): BelongsTo
    {
        return $this->belongsTo(OperationalStatus::class, 'operational_status_id');
    }
}
