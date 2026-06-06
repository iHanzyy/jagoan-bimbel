<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MateriCollection;
use App\Http\Resources\MateriResource;
use App\Models\FileMateri;
use App\Models\User;
use App\Services\MateriService;
use Illuminate\Http\Request;

final class SiswaMateriController extends Controller
{
    public function __construct(
        private readonly MateriService $materiService,
    ) {
    }

    public function index(Request $request): MateriCollection
    {
        return new MateriCollection($this->materiService->list(
            role: $this->userRole($request),
            page: (int) $request->integer('page', 1),
        ));
    }

    public function show(FileMateri $fileMateri): MateriResource
    {
        return (new MateriResource($this->materiService->findById((int) $fileMateri->getKey())))
            ->additional(['meta' => [], 'message' => 'Detail materi berhasil diambil.']);
    }

    private function userRole(Request $request): string
    {
        /** @var User $user */
        $user = $request->user();

        return (string) $user->role;
    }
}
