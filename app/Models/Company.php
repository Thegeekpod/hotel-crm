<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $fillable = [
        'name',
        'address',
        'gstin',
        'phone',
        'discount_type',
        'discount_value',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'float',
    ];

    public function getFormattedDiscountAttribute(): string
    {
        $val = (float)($this->discount_value ?? 0);
        if ($val <= 0) {
            return 'No Discount';
        }
        if ($this->discount_type === 'amount') {
            return '₹ ' . number_format($val, 2);
        }
        return $val . '%';
    }
}
