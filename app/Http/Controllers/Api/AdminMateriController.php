<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMateriRequest;
use App\Http\Resources\MateriCollection;
use App\Http\Resources\MateriResource;
use App\Models\FileMateri;
use App\Services\MateriService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AdminMateriController extends Controller
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

    public function show(FileMateri $fileMateri): MateriResource
    {
        return (new MateriResource($this->materiService->findById((int) $fileMateri->getKey())))
            ->additional(['meta' => [], 'message' => 'Detail materi berhasil diambil.']);
    }

    public function store(StoreMateriRequest $request): JsonResponse
    {
        $materi = $this->materiService->create($request->toCreateData(
            createdBy: (int) $request->user()->getAuthIdentifier(),
        ));

        return (new MateriResource($materi))
            ->additional(['meta' => [], 'message' => 'Materi berhasil diunggah.'])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function download(FileMateri $fileMateri): StreamedResponse
    {
        $downloadable = $this->materiService->getDownloadable((int) $fileMateri->getKey());

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($downloadable->disk);

        return $disk->download(
            path: $downloadable->path,
            name: $downloadable->downloadName,
        );
    }
}
