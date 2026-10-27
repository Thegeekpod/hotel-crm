<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSupplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'gst_number',
        'address',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(StoreItem::class, 'default_supplier_id');
    }

    public function requisitions()
    {
        return $this->hasMany(StoreRequisition::class, 'store_supplier_id');
    }
}
