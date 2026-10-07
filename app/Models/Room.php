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
        'operational_status_id',
        'housekeeping_state_id',
        'floor',
        'category',
        'bedding_config',
        'status',
        'housekeeping_status',
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

    public function operationalStatusRelation(): BelongsTo
    {
        return $this->belongsTo(OperationalStatus::class, 'operational_status_id');
    }

    public function housekeepingStateRelation(): BelongsTo
    {
        return $this->belongsTo(HousekeepingState::class, 'housekeeping_state_id');
    }
}
