<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\AuthTokenData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

final class AuthService
{
    private const TOKEN_NAME = 'api-token';

    public function login(string $email, string $password): AuthTokenData
    {
        $user = User::query()->where('email', $email)->first();

        if (! $user instanceof User || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken(
            name: self::TOKEN_NAME,
            abilities: [$user->role],
        )->plainTextToken;

        return new AuthTokenData(
            userId: (int) $user->getKey(),
            name: (string) $user->name,
            email: (string) $user->email,
            role: (string) $user->role,
            token: $token,
            tokenType: 'Bearer',
        );
    }

    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if (! $token instanceof PersonalAccessToken) {
            return;
        }

        $token->delete();
    }
}
