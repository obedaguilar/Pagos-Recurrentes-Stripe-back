<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suscripcion extends Model
{
    protected $table = 'subscripciones';
    protected $fillable = [
        'id_suscripcion',
        'title_suscripcion',
        'customer',
        'customer_email',
        'customer_id_object_subs',
        'status_factura',
    ];
}
