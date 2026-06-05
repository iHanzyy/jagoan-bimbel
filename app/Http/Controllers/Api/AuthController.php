<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthTokenResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    public function login(LoginRequest $request): AuthTokenResource
    {
        return (new AuthTokenResource($this->authService->login($request->email(), $request->password())))
            ->additional(['meta' => [], 'message' => 'Login berhasil']);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($this->authenticatedUser($request));

        return response()->json(['data' => null, 'meta' => [], 'message' => 'Logout berhasil']);
    }

    private function authenticatedUser(Request $request): User
    {
        /** @var User $user */
        $user = $request->user();

        return $user;
    }
}
