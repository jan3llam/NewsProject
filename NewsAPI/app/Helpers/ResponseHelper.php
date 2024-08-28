<?php

namespace App\Helpers;

use App\Enums\HttpResponseEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ResponseHelper
{
    public static function successResponse(string $channel, string $message, array $data = [], int $statusCode = HttpResponseEnum::OK->value): JsonResponse
    {
        Log::channel($channel)->info($message);

        return response()->json([
            'message' => $message,
            'data' => $data,
            'error' => null
        ], $statusCode);
    }


    public static function errorResponse(string $channel, string $message, string $error, int $statusCode = HttpResponseEnum::BAD_REQUEST->value): JsonResponse
    {
        Log::channel($channel)->error($message, ['error' => $error]);

        return response()->json([
            'message' => $message,
            'data' => null,
            'error' => $error
        ], $statusCode);
    }
}
