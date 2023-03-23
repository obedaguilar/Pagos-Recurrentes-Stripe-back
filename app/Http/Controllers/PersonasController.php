<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;

class PersonasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function ObtenerPersonas()
    {
     try {
        return User::all();

     } catch (Exception $th) {
        //throw $th;
     }

    }
}
