<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\FileMateri;
use App\Models\User;

final class MateriPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array(
            needle: $user->role,
            haystack: [
                User::ROLE_ADMIN,
                User::ROLE_SISWA,
            ],
            strict: true,
        );
    }

    public function view(User $user, FileMateri $fileMateri): bool
    {
        return in_array(
            needle: $user->role,
            haystack: [
                User::ROLE_ADMIN,
                User::ROLE_SISWA,
            ],
            strict: true,
        );
    }

    public function create(User $user): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    public function update(User $user, FileMateri $fileMateri): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    public function delete(User $user, FileMateri $fileMateri): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    public function download(User $user, FileMateri $fileMateri): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }
}
