<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\HttpResponseEnum;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class CheckAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $token = $request->bearerToken();

        if (!$token) {
            return ResponseHelper::errorResponse(
                'auth',
                'Token is missing',
                'You need to log in to access this resource.',
                HttpResponseEnum::UNAUTHORIZED->value
            );
        }

        $personalAccessToken = PersonalAccessToken::findToken($token);

        if (!$personalAccessToken || $personalAccessToken->tokenable_id !== $user->id) {
            return ResponseHelper::errorResponse(
                'auth',
                'Invalid or mismatched token',
                'You need to log in to access this resource.',
                HttpResponseEnum::UNAUTHORIZED->value
            );
        }

        return $next($request);
    }
}
