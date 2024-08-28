<?php

namespace App\Http\Controllers\Api;

use App\Enums\HttpResponseEnum;
use App\Helpers\ResponseHelper;
use App\Services\Api\AuthService;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    protected $authService;
    protected $responseHelper;

    public function __construct(AuthService $authService, ResponseHelper $responseHelper)
    {
        $this->authService = $authService;
        $this->responseHelper = $responseHelper;
    }

    public function register(RegisterRequest $request)
    {
        try {
            Log::channel('auth')->info('User Registeration Validation Success');

            $validatedData = $request->validated();

            $user = $this->authService->register($validatedData);

            return $this->responseHelper->successResponse(
                'auth',
                'User registered successfully',
                ['user' => $user],
                HttpResponseEnum::CREATED->value
            );
        } catch (\Exception $e) {

            return $this->responseHelper->errorResponse(
                'auth',
                'User registration failed',
                $e->getMessage(),
                HttpResponseEnum::UNPROCESSABLE_CONTENT->value
            );
        }
    }

    public function login(AuthRequest $request)
    {
        try {
            Log::channel('auth')->info('Login Validation Success');

            $validatedData = $request->validated();

            $data = $this->authService->login($validatedData);

            return $this->responseHelper->successResponse(
                'auth',
                'User logged in successfully',
                $data,
                HttpResponseEnum::OK->value
            );
        } catch (\Exception $e) {

            return $this->responseHelper->errorResponse(
                'auth',
                $e->getMessage(),
                $e->getMessage(),
                HttpResponseEnum::UNAUTHORIZED->value
            );
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->responseHelper->errorResponse(
                    'auth',
                    'Logout failed',
                    'No authenticated user found.',
                    HttpResponseEnum::UNAUTHORIZED->value
                );
            }
            $this->authService->logout($user);

            return $this->responseHelper->successResponse(
                'auth',
                'User logged out successfully',
                ['user_id' => $user->id, 'email' => $user->email],
                HttpResponseEnum::OK->value
            );
        } catch (\Exception $e) {

            return $this->responseHelper->errorResponse(
                'auth',
                'User logout failed',
                $e->getMessage(),
                HttpResponseEnum::UNPROCESSABLE_CONTENT->value
            );
        }
    }
}
