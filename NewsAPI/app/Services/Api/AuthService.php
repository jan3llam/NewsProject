<?php

namespace App\Services\Api;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class AuthService
{

    public function register(array $data): User
    {

        Log::channel('auth')->info('Creating new user');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Log::channel('auth')->info('New user registered', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return $user;
    }


    public function login(array $data): array
    {

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            Log::channel('auth')->warning('Failed login attempt', [
                'email' => $data['email'],
                'error' => 'Invalid credentials',
            ]);

            throw new Exception('Invalid credentials');
        }

        $token = $user->createToken('API Token')->plainTextToken;

        Log::channel('auth')->info('User logged in', [
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $token,
        ]);

        return [
            'user' => $user->id,
            'token' => $token
        ];
    }


    public function logout(User $user): void
    {
        $user->tokens()->delete();

        Log::channel('auth')->info('User tokens revoked', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }
}
