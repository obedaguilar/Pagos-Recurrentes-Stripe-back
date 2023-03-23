<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;

class Controller extends BaseController
{
    protected function respondWithToken($token)
    {
        $user = User::where('api_token', $token)->first();
        if($user){
            return User::where('objectId', $user->objectId)->first();
        }
        return User::where('api_token', $token)->first();
    }
}
