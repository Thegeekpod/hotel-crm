<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'store_category_id',
        'pos_item_id',
        'default_supplier_id',
        'unit',
        'plates_per_unit',
        'unit_cost',
        'current_stock',
        'min_threshold',
        'location',
        'description',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(StoreCategory::class, 'store_category_id');
    }

    public function posItem()
    {
        return $this->belongsTo(PosItem::class, 'pos_item_id');
    }

    public function supplier()
    {
        return $this->belongsTo(StoreSupplier::class, 'default_supplier_id');
    }

    public function movements()
    {
        return $this->hasMany(StoreMovement::class, 'store_item_id');
    }

    public function requisitions()
    {
        return $this->hasMany(StoreRequisition::class, 'store_item_id');
    }

    public function recipes()
    {
        return $this->hasMany(PosItemIngredient::class, 'store_item_id');
    }

    /**
     * Estimated Plates / Servings available from current stock ("Kota plate hobe")
     */
    public function getTotalPlatesAttribute()
    {
        $yield = (float)($this->plates_per_unit ?: 1.0);
        return (int) floor($this->current_stock * $yield);
    }

    /**
     * Helper to compute dynamic stock health status
     */
    public function getStockStatusAttribute()
    {
        if ($this->current_stock <= 0) {
            return 'Out of Stock';
        }
        if ($this->current_stock <= $this->min_threshold) {
            return 'Low Stock Alert';
        }
        return 'Optimal';
    }
}
