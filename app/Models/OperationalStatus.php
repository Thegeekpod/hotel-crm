<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalStatus extends Model
{
    use HasFactory;

    protected $table = 'operational_statuses';

    protected $fillable = [
        'name',
        'code',
        'badge_color',
        'status',
    ];
}
