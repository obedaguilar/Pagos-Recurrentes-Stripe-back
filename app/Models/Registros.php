<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registros extends Model
{

    use HasFactory;

    protected $fillable = [
        'objectId',
        'nombre',
        'apellidoP',
        'apellidoM',
        'password',
        'email',
        'telefono',
        'user_roles_objectId',

    ];

    protected $hidden = [
        'password',
    ];
}
