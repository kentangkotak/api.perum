<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;

class RefreshToken
{
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            try {
                $newToken = JWTAuth::refresh(JWTAuth::getToken());
                $response = $next($request);
                return $response->header('Authorization', 'Bearer ' . $newToken);
            } catch (JWTException $e) {
                return response()->json(['message' => 'Token expired, please login again'], 401);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Token invalid or missing'], 401);
        }

        return $next($request);
    }
}
