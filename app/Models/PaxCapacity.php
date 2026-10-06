<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaxCapacity extends Model
{
    use HasFactory;

    protected $table = 'pax_capacities';

    protected $fillable = [
        'name',
        'max_adults',
        'max_children',
        'max_total',
        'status',
    ];
}
