<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(StoreItem::class, 'store_category_id');
    }

    public function posItems()
    {
        return $this->hasMany(PosItem::class, 'category', 'name');
    }
}
