<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cards extends Model
{
    protected $table = 'cards';

    protected $fillable = [
        'metodo_pago_id',
        'description_payment',
        'tipo_card',
        'country_stripe',
        'exp_month',
        'exp_year',
        'fondos',
        'cuatro_digitos',
        'fecha_creacion',
        'customer_id_object',
    ];
}
