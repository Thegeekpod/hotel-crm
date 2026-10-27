<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_item_id',
        'pos_item_id',
        'type', // IN, OUT, ADJUSTMENT
        'quantity',
        'stock_before',
        'stock_after',
        'reference_no',
        'issued_to_department',
        'issued_to_person',
        'unit_cost',
        'remarks',
        'performed_by',
    ];

    public function item()
    {
        return $this->belongsTo(StoreItem::class, 'store_item_id');
    }

    public function posItem()
    {
        return $this->belongsTo(PosItem::class, 'pos_item_id');
    }
}
