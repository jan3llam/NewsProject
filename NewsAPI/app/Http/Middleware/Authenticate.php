<?php

namespace App\Http\Middleware;

use App\Enums\HttpResponseEnum;
use App\Helpers\ResponseHelper;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Handle an unauthenticated request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|null
     */
    protected function unauthenticated($request, array $guards): ?JsonResponse
    {
        if ($request->expectsJson() || Auth::user() === null) {
            return ResponseHelper::errorResponse(
                'auth',
                'Authentication Required',
                'You need to log in to access this resource.',
                HttpResponseEnum::UNAUTHORIZED->value
            );
        }

        return null;
    }
}
