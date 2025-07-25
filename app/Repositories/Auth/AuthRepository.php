<?php

namespace App\Repositories\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthRepository
{
    /**
     * Check if the user is logged in.
     */
    public function isLogin(): bool
    {
        return Auth::check();
    }

    /**
     * Get authenticated user login id
     */
    public function getUserId(): int
    {
        return Auth::user()->id;
    }

    /**
     * Do login action with matching between email and password.
     */
    public function doLogin(string $email, string $password): bool
    {
        $authAttempt = Auth::attempt([
            'email' => $email,
            'password' => $password,
        ]);

        return $authAttempt;
    }

    /**
     * Get login user data after attempt login request
     */
    public function loginUser(): ?array
    {
        $user = Auth::user();
        if ($user === null) {
            return null;
        }

        $token = $user->createToken(Str::random(120))->plainTextToken;

        return [
            'user' => $user,
            'token' => [
                'type' => 'Bearer',
                'value' => $token,
            ],
        ];
    }
}
