<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\DownloadableMateriData;
use App\Data\MateriCreateData;
use App\Data\MateriData;
use App\Data\MateriListData;
use App\Data\MateriUpdateData;
use App\Enums\MateriType;
use App\Models\FileMateri;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class MateriService
{
    private const PUBLIC_DISK = 'public';

    private const DEFAULT_PER_PAGE = 10;

    public function list(int $page = 1, int $perPage = self::DEFAULT_PER_PAGE): MateriListData
    {
        $paginator = FileMateri::query()
            ->latest()
            ->paginate(
                perPage: $perPage,
                columns: ['*'],
                pageName: 'page',
                page: $page,
            );

        return $this->toListData($paginator);
    }

    public function findById(int $id): MateriData
    {
        $materi = FileMateri::query()->find($id);

        if (! $materi instanceof FileMateri) {
            throw new NotFoundHttpException('Materi tidak ditemukan.');
        }

        return MateriData::fromModel($materi);
    }

    public function create(MateriCreateData $data): MateriData
    {
        $this->ensureValidCreatePayload($data);

        return DB::transaction(function () use ($data): MateriData {
            $filePath = $this->storeFileIfNeeded(
                type: $data->type,
                file: $data->file,
            );

            $materi = FileMateri::query()->create([
                'title' => $data->title,
                'description' => $data->description,
                'type' => $data->type,
                'file_path' => $filePath,
                'youtube_url' => $data->type->isYoutube() ? $data->youtubeUrl : null,
                'created_by' => $data->createdBy,
            ]);

            return MateriData::fromModel($materi->refresh());
        });
    }

    public function update(int $id, MateriUpdateData $data): MateriData
    {
        $materi = FileMateri::query()->find($id);

        if (! $materi instanceof FileMateri) {
            throw new NotFoundHttpException('Materi tidak ditemukan.');
        }

        $this->ensureValidUpdatePayload(
            materi: $materi,
            data: $data,
        );

        return DB::transaction(function () use ($materi, $data): MateriData {
            $oldFilePath = $materi->file_path;

            $newFilePath = $this->resolveUpdatedFilePath(
                materi: $materi,
                type: $data->type,
                file: $data->file,
            );

            $materi->update([
                'title' => $data->title,
                'description' => $data->description,
                'type' => $data->type,
                'file_path' => $data->type->isFileBased() ? $newFilePath : null,
                'youtube_url' => $data->type->isYoutube() ? $data->youtubeUrl : null,
            ]);

            $this->deleteOldFileIfReplaced(
                oldFilePath: $oldFilePath,
                newFilePath: $materi->file_path,
            );

            return MateriData::fromModel($materi->refresh());
        });
    }

    public function delete(int $id): void
    {
        $materi = FileMateri::query()->find($id);

        if (! $materi instanceof FileMateri) {
            throw new NotFoundHttpException('Materi tidak ditemukan.');
        }

        DB::transaction(function () use ($materi): void {
            $filePath = $materi->file_path;

            $materi->deleteOrFail();

            if ($filePath !== null) {
                Storage::disk(self::PUBLIC_DISK)->delete($filePath);
            }
        });
    }

    public function getDownloadable(int $id): DownloadableMateriData
    {
        $materi = FileMateri::query()->find($id);

        if (! $materi instanceof FileMateri) {
            throw new NotFoundHttpException('Materi tidak ditemukan.');
        }

        if (! $materi->type->isFileBased() || $materi->file_path === null) {
            throw new InvalidArgumentException('Materi Youtube tidak dapat diunduh sebagai file.');
        }

        if (! Storage::disk(self::PUBLIC_DISK)->exists($materi->file_path)) {
            throw new RuntimeException('File materi tidak ditemukan di storage.');
        }

        return new DownloadableMateriData(
            disk: self::PUBLIC_DISK,
            path: $materi->file_path,
            downloadName: $this->buildDownloadName($materi),
        );
    }

    private function ensureValidCreatePayload(MateriCreateData $data): void
    {
        if ($data->type->isYoutube()) {
            $this->ensureYoutubePayloadIsValid(
                file: $data->file,
                youtubeUrl: $data->youtubeUrl,
            );

            return;
        }

        $this->ensureFilePayloadIsValid(
            type: $data->type,
            file: $data->file,
            youtubeUrl: $data->youtubeUrl,
            fileIsRequired: true,
        );
    }

    private function ensureValidUpdatePayload(FileMateri $materi, MateriUpdateData $data): void
    {
        if ($data->type->isYoutube()) {
            $this->ensureYoutubePayloadIsValid(
                file: $data->file,
                youtubeUrl: $data->youtubeUrl,
            );

            return;
        }

        $fileIsRequired = $materi->type !== $data->type || $materi->file_path === null;

        $this->ensureFilePayloadIsValid(
            type: $data->type,
            file: $data->file,
            youtubeUrl: $data->youtubeUrl,
            fileIsRequired: $fileIsRequired,
        );
    }

    private function ensureYoutubePayloadIsValid(?UploadedFile $file, ?string $youtubeUrl): void
    {
        if ($file !== null) {
            throw new InvalidArgumentException('Materi Youtube tidak boleh memiliki file.');
        }

        if ($youtubeUrl === null || trim($youtubeUrl) === '') {
            throw new InvalidArgumentException('URL Youtube wajib diisi.');
        }
    }

    private function ensureFilePayloadIsValid(
        MateriType $type,
        ?UploadedFile $file,
        ?string $youtubeUrl,
        bool $fileIsRequired,
    ): void {
        if ($youtubeUrl !== null && trim($youtubeUrl) !== '') {
            throw new InvalidArgumentException('Materi PDF atau Image tidak boleh memiliki URL Youtube.');
        }

        if ($fileIsRequired && $file === null) {
            throw new InvalidArgumentException('File wajib diunggah untuk materi PDF atau Image.');
        }

        if ($file === null) {
            return;
        }

        if (! in_array($file->getMimeType(), $type->acceptedMimeTypes(), true)) {
            throw new InvalidArgumentException('Tipe file tidak sesuai dengan tipe materi.');
        }
    }

    private function storeFileIfNeeded(MateriType $type, ?UploadedFile $file): ?string
    {
        if (! $type->isFileBased()) {
            return null;
        }

        if (! $file instanceof UploadedFile) {
            throw new InvalidArgumentException('File wajib diunggah.');
        }

        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid()->toString() . ".{$extension}";

        return $file->storeAs(
            path: $type->storageDirectory(),
            name: $filename,
            options: self::PUBLIC_DISK,
        );
    }

    private function resolveUpdatedFilePath(
        FileMateri $materi,
        MateriType $type,
        ?UploadedFile $file,
    ): ?string {
        if ($type->isYoutube()) {
            return null;
        }

        if ($file instanceof UploadedFile) {
            return $this->storeFileIfNeeded(
                type: $type,
                file: $file,
            );
        }

        if ($materi->type !== $type || $materi->file_path === null) {
            throw new InvalidArgumentException('File wajib diunggah saat mengganti tipe materi.');
        }

        return $materi->file_path;
    }

    private function deleteOldFileIfReplaced(?string $oldFilePath, ?string $newFilePath): void
    {
        if ($oldFilePath === null) {
            return;
        }

        if ($oldFilePath === $newFilePath) {
            return;
        }

        Storage::disk(self::PUBLIC_DISK)->delete($oldFilePath);
    }

    private function buildDownloadName(FileMateri $materi): string
    {
        $extension = pathinfo((string) $materi->file_path, PATHINFO_EXTENSION);
        $safeTitle = Str::slug((string) $materi->title);

        return "{$safeTitle}.{$extension}";
    }

    private function toListData(LengthAwarePaginator $paginator): MateriListData
    {
        /** @var Collection<int, MateriData> $items */
        $items = $paginator->getCollection()
            ->map(static fn (FileMateri $materi): MateriData => MateriData::fromModel($materi));

        return new MateriListData(
            items: $items,
            meta: [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
            ],
        );
    }
}
