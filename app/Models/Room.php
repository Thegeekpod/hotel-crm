<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'room_number',
        'floor_id',
        'category_id',
        'rate',
        'bedding_config_id',
        'floor',
        'category',
        'bedding_config',
        'status',
        'amenities',
        'notes',
    ];

    protected $casts = [
        'amenities' => 'array',
        'rate' => 'decimal:2',
    ];

    /**
     * Relationships matching Add Room Modal foreign keys
     */
    public function floorRelation(): BelongsTo
    {
        return $this->belongsTo(Floor::class, 'floor_id');
    }

    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class, 'category_id');
    }

    public function beddingConfigRelation(): BelongsTo
    {
        return $this->belongsTo(BeddingConfig::class, 'bedding_config_id');
    }

    public function maintenances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RoomMaintenance::class, 'room_id');
    }

    public function latestMaintenance(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(RoomMaintenance::class, 'room_id')->latestOfMany();
    }

    public function housekeepingHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RoomHousekeepingHistory::class, 'room_id');
    }

    public function operationalHistories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RoomOperationalHistory::class, 'room_id');
    }
}

