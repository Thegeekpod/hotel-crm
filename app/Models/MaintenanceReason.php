<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceReason extends Model
{
    use HasFactory;

    protected $table = 'maintenance_reasons';

    protected $fillable = [
        'name',
        'dept',
        'priority',
        'sla',
        'status',
    ];
}
