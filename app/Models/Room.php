<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'room_number',
        'floor_id',
        'category_id',
        'bedding_config_id',
        'rate',
        'status',
        'amenities',
        'notes',
    ];

    protected $casts = [
        'amenities' => 'array',
        'rate' => 'decimal:2',
    ];

    protected $appends = [
        'floor',
        'category',
        'bedding_config',
    ];

    /**
     * Dynamic Accessors for legacy compatibility & view bindings
     */
    public function getFloorAttribute(): ?string
    {
        return $this->floorRelation?->floor ? (string)$this->floorRelation->floor : null;
    }

    public function getCategoryAttribute(): ?string
    {
        return $this->categoryRelation?->name ?? null;
    }

    public function getBeddingConfigAttribute(): ?string
    {
        return $this->beddingConfigRelation?->name ?? null;
    }

    public function getAmenitiesDataAttribute(): \Illuminate\Database\Eloquent\Collection
    {
        $ids = is_array($this->amenities) ? $this->amenities : [];
        return Amenity::whereIn('id', $ids)->get();
    }

    public function getAmenitiesNamesAttribute(): array
    {
        $ids = is_array($this->amenities) ? $this->amenities : [];
        return Amenity::whereIn('id', $ids)->pluck('name')->toArray();
    }

    /**
     * Relationships matching foreign keys
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

    public function maintenances(): HasMany
    {
        return $this->hasMany(RoomMaintenance::class, 'room_id');
    }

    public function latestMaintenance(): HasOne
    {
        return $this->hasOne(RoomMaintenance::class, 'room_id')->latestOfMany();
    }

    public function housekeepingHistories(): HasMany
    {
        return $this->hasMany(RoomHousekeepingHistory::class, 'room_id');
    }

    public function operationalHistories(): HasMany
    {
        return $this->hasMany(RoomOperationalHistory::class, 'room_id');
    }
}

