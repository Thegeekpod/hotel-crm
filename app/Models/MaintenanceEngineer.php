<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceEngineer extends Model
{
    use HasFactory;

    protected $table = 'maintenance_engineers';

    protected $fillable = [
        'name',
        'department',
        'phone',
        'status',
    ];
}
