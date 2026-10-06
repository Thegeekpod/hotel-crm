<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeddingConfig extends Model
{
    use HasFactory;

    protected $table = 'bedding_configs';

    protected $fillable = [
        'name',
        'code',
        'dimensions',
        'status',
    ];
}
