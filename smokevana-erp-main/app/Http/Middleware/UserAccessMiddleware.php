<?php

namespace App\Http\Middleware;

use App\StaffAuth;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->bearerToken()) {
            return response()->json([
                'status' => false,
                'message' => 'No API token provided.',
            ],401);
        }
        try {
            //code...
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user || !$user->allow_login==1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthenticated.',
                ],401);
            }else{
                $request->merge([
                    'current_user' => $user
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Fail to check your unknow auth token please login again.',
            ],401);
        }
        
        return $next($request);
    }
    
}
