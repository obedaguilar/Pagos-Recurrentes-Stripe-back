<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planes extends Model
{
    protected $table = 'plans';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'stripe_id',
        'amount',
        'interval',
        'currency',
        'status',
        'slug',
        'description',
        'created_at',
        'updated_at',
    ];
}
