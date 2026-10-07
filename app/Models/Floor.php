<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model
{
    use HasFactory;

    protected $table = 'floors';

    protected $fillable = [
        'floor',
        'name',
        'rooms',
        'status',
    ];

    protected $casts = [
        'rooms' => 'integer',
    ];

    public function roomsRelation(): HasMany
    {
        return $this->hasMany(Room::class, 'floor_id');
    }

    /**
     * Compute current room count dynamically for this floor
     */
    public function getCalculatedRoomsCountAttribute(): int
    {
        return Room::where('floor_id', $this->id)
            ->orWhere('floor', $this->floor)
            ->orWhere('floor', $this->name)
            ->count();
    }
}
