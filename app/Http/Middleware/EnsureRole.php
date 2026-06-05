<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureRole
{
    /**
     * @param Closure(Request): Response $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user instanceof User) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthenticated.');
        }

        if (! in_array($user->role, $roles, true)) {
            abort(Response::HTTP_FORBIDDEN, 'Akses tidak diizinkan.');
        }

        return $next($request);
    }
}
