<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableManage extends Model
{
    use HasFactory;

    protected $table = 'table_manages';

    protected $fillable = [
        'table_number',
        'table_name',
        'status',
    ];

    /**
     * Scope for tables that are currently available to be booked.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * All POS orders for this table.
     */
    public function posOrders()
    {
        return $this->hasMany(PosOrder::class, 'table_id');
    }

    /**
     * Currently active pending KOTs on this table (not yet settled in a final bill).
     */
    public function activeKots()
    {
        return $this->hasMany(PosOrder::class, 'table_id')
            ->where('is_kot', true)
            ->whereNull('parent_order_id')
            ->where('status', '!=', 'Cancelled')
            ->where('payment_status', 'Pending');
    }
}
