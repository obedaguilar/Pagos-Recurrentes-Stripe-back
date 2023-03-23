<?php

namespace App\Models;

use DeepCopy\Matcher\PropertyTypeMatcher;
use Illuminate\Database\Eloquent\Model;

class Pagos extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'nombre_cliente',
        'email_cliente',
        'status_pago',
        'id_pago',
        'amount',
        'suscripcion',
        'stripe_id_plan',
        'plan_name',
        'fecha_pago',
        'fecha_inicio',
        'fecha_vencimiento',
    ];
}
