<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NightAudit extends Model
{
    use HasFactory;

    protected $table = 'night_audits';

    protected $fillable = [
        'audit_date',
        'completed_at',
        'auditor_id',
        'auditor_name',
        'status',
        'total_payments',
        'advance_payments',
        'settlement_payments',
        'cash_collection',
        'upi_collection',
        'card_collection',
        'bank_collection',
        'other_collection',
        'room_revenue',
        'food_revenue',
        'extra_revenue',
        'tax_amount',
        'discount_amount',
        'total_revenue',
        'total_rooms',
        'occupied_rooms',
        'vacant_rooms',
        'maintenance_rooms',
        'dirty_rooms',
        'clean_rooms',
        'occupancy_rate',
        'adr',
        'revpar',
        'arrivals_count',
        'departures_count',
        'inhouse_guests_count',
        'stay_over_count',
        'cancelled_count',
        'pending_arrivals',
        'pending_departures',
        'ep_plan_count',
        'cp_plan_count',
        'map_plan_count',
        'ap_plan_count',
        'remarks',
        'audit_snapshot',
    ];

    protected $casts = [
        'audit_date' => 'date',
        'completed_at' => 'datetime',
        'total_payments' => 'decimal:2',
        'advance_payments' => 'decimal:2',
        'settlement_payments' => 'decimal:2',
        'cash_collection' => 'decimal:2',
        'upi_collection' => 'decimal:2',
        'card_collection' => 'decimal:2',
        'bank_collection' => 'decimal:2',
        'other_collection' => 'decimal:2',
        'room_revenue' => 'decimal:2',
        'food_revenue' => 'decimal:2',
        'extra_revenue' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'occupancy_rate' => 'decimal:2',
        'adr' => 'decimal:2',
        'revpar' => 'decimal:2',
        'total_rooms' => 'integer',
        'occupied_rooms' => 'integer',
        'vacant_rooms' => 'integer',
        'maintenance_rooms' => 'integer',
        'dirty_rooms' => 'integer',
        'clean_rooms' => 'integer',
        'arrivals_count' => 'integer',
        'departures_count' => 'integer',
        'inhouse_guests_count' => 'integer',
        'stay_over_count' => 'integer',
        'cancelled_count' => 'integer',
        'pending_arrivals' => 'integer',
        'pending_departures' => 'integer',
        'ep_plan_count' => 'integer',
        'cp_plan_count' => 'integer',
        'map_plan_count' => 'integer',
        'ap_plan_count' => 'integer',
        'audit_snapshot' => 'array',
    ];

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }
}
