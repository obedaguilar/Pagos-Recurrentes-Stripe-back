<?php

namespace App\Http\Middleware;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Closure;

class AdminMiddleware
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
            Log::debug($request->header('Authorization'));

            $user = User::where('api_token', $request->header('Authorization'))->where('isDeleted', false)->first();
            if($user && $user->user_roles_objectId == config('app.rolesCall.superAdmin')) {
                return $next($request);
            }
            Log::debug($user);
        }

        return response()->json(['message' => 'Tu no eres un super admin.'], 401);
    }
}
