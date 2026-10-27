<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosItemIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'pos_item_id',
        'store_item_id',
        'quantity_per_plate',
        'unit',
    ];

    public function posItem()
    {
        return $this->belongsTo(PosItem::class, 'pos_item_id');
    }

    public function storeItem()
    {
        return $this->belongsTo(StoreItem::class, 'store_item_id');
    }
}
