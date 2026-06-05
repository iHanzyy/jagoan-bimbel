<?php

declare(strict_types=1);

namespace App\Data;

final readonly class AuthTokenData
{
    public function __construct(
        public int $userId,
        public string $name,
        public string $email,
        public string $role,
        public string $token,
        public string $tokenType,
    ) {
    }
}
