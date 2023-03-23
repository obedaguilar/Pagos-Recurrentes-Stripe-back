<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facturas extends Model
{
    protected $table = 'facturas_clientes';
    protected $fillable = [
        'fecha_factura',
        'mes_factura' ,
        'nombre_cliente' ,
        'user_invoice_objectId' ,
        'email_cliente',
        'nombre_factura',
        'url_factura',
    ];
}
