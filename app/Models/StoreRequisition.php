<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreRequisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_no',
        'store_item_id',
        'store_supplier_id',
        'quantity_requested',
        'estimated_cost',
        'priority',
        'status',
        'requested_by',
        'notes',
        'required_by_date',
    ];

    public function item()
    {
        return $this->belongsTo(StoreItem::class, 'store_item_id');
    }

    public function supplier()
    {
        return $this->belongsTo(StoreSupplier::class, 'store_supplier_id');
    }
}
