<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Token is missing'
            ], 401);
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Token is missing'
            ], 401);
        }

        if ($accessToken->expires_at && now()->greaterThanOrEqualTo($accessToken->expires_at)) {
            $accessToken->delete();

            return response()->json([
                'status' => false,
                'message' => 'Unauthorized: Token expired, please login again'
            ], 401);
        }

        $user = $accessToken->tokenable;

        Auth::setUser($user);

        return $next($request);
    }
}
