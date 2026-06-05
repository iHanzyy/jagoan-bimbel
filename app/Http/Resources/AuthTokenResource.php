<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Data\AuthTokenData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use RuntimeException;

final class AuthTokenResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $authToken = $this->authTokenData();

        return [
            'user' => [
                'id' => $authToken->userId,
                'name' => $authToken->name,
                'email' => $authToken->email,
                'role' => $authToken->role,
            ],
            'access_token' => $authToken->token,
            'token_type' => $authToken->tokenType,
        ];
    }

    private function authTokenData(): AuthTokenData
    {
        if (! $this->resource instanceof AuthTokenData) {
            throw new RuntimeException('AuthTokenResource expects AuthTokenData.');
        }

        return $this->resource;
    }
}
