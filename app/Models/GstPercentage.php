<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GstPercentage extends Model
{
    use HasFactory;

    protected $table = 'gst_percentages';

    protected $fillable = [
        'gst_percentage',
        'status',
    ];

    protected $casts = [
        'gst_percentage' => 'float',
    ];
}
