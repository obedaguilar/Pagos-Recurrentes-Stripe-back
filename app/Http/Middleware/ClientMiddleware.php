<?php

namespace App\Http\Middleware;
use App\Models\User;
use Illuminate\Support\Facades\Log;

use Closure;

class ClientMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('Authorization')) {
            // Log::debug($request->header('Authorization'));

            $user = User::where('api_token', $request->header('Authorization'))->where('isDeleted', false)->first();
            if($user && $user->user_roles_objectId == config('app.rolesCall.cliente')) {
                return $next($request);
            }
            Log::debug($user);
        }

        return response()->json(['message' => 'Usuario no autorizado.'], 401);
    }
}
