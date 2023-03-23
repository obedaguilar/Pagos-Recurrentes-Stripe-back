<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;


    protected $primaryKey = 'objectId';
    public $incrementing = false;
    protected $keyType = 'string';
    /* The model's default values for attributes. @var array*/
    protected $attributes = [
        'isActive' => 1, 'isDeleted' => false,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'objectId',
        'nombre',
        'apellidoP',
        'apellidoM',
        'password',
        'email',
        'telefono',
        'api_token',
        'user_roles_objectId',
        'isActive',
        'isDeleted',
        'documento',
        'stripe_customer_id',

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [
        'password',
    ];
}
