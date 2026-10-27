<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category',
        'price',
        'emoji',
        'description',
        'ingredient_name',
        'raw_stock_qty',
        'raw_stock_unit',
        'per_plate_qty',
        'per_plate_unit',
        'available_plates',
        'min_alert_plates',
        'status',
    ];

    /**
     * Compute current plate stock health status
     */
    public function getPlateStatusAttribute()
    {
        if ($this->available_plates <= 0) {
            return 'Out of Stock';
        }
        if ($this->available_plates <= ($this->min_alert_plates ?: 10)) {
            return 'Low Stock Alert';
        }
        return 'Optimal';
    }

    /**
     * Formatted ingredient per plate description
     */
    public function getIngredientDisplayAttribute()
    {
        if (!$this->ingredient_name) {
            return 'No ingredient configured';
        }
        $perPlate = (float) $this->per_plate_qty;
        $unit = $this->per_plate_unit ?: 'gm';
        return "{$this->ingredient_name} ({$perPlate} {$unit} / plate)";
    }

    public function orderItems()
    {
        return $this->hasMany(PosOrderItem::class, 'pos_item_id');
    }

    public function ingredients()
    {
        return $this->hasMany(PosItemIngredient::class, 'pos_item_id');
    }

    public function storeItem()
    {
        return $this->hasOne(StoreItem::class, 'pos_item_id');
    }

    /**
     * Compute maximum plates/servings that can be cooked based on store stock
     */
    public function getEstimatedPlatesAttribute()
    {
        // If directly mapped to a store item
        if ($this->storeItem) {
            return $this->storeItem->total_plates;
        }

        // If mapped via recipe ingredients
        if ($this->ingredients->isNotEmpty()) {
            $minPlates = null;
            foreach ($this->ingredients as $recipe) {
                $ing = $recipe->storeItem;
                if (!$ing || $recipe->quantity_per_plate <= 0) continue;
                $possiblePlates = (int) floor($ing->current_stock / $recipe->quantity_per_plate);
                if ($minPlates === null || $possiblePlates < $minPlates) {
                    $minPlates = $possiblePlates;
                }
            }
            return $minPlates !== null ? $minPlates : 50;
        }

        return null;
    }
}
