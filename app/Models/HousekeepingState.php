<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HousekeepingState extends Model
{
    use HasFactory;

    protected $table = 'housekeeping_states';

    protected $fillable = [
        'name',
        'badge_color',
        'status',
    ];
}
