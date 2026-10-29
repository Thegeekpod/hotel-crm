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
}
