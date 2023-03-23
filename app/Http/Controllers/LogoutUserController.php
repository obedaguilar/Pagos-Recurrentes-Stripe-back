<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LogoutUserController extends Controller{

    public function logout(){


        $user = User::find(auth()->user()->objectId);
        $user->api_token = null;
        $user->save();



        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada'
        ], 200);
    }
}
