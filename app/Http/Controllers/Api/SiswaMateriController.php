<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MateriCollection;
use App\Http\Resources\MateriResource;
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
            page: (int) $request->integer('page', 1),
        ));
    }

    public function show(int $id): MateriResource
    {
        return (new MateriResource($this->materiService->findById($id)))
            ->additional(['meta' => [], 'message' => 'Detail materi berhasil diambil.']);
    }
}
