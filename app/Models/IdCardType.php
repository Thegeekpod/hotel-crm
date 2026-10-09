<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdCardType extends Model
{
    use HasFactory;

    protected $table = 'id_card_types';

    protected $fillable = [
        'name',
        'status',
    ];
}
