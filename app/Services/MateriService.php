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
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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

    private const LIST_CACHE_TTL_SECONDS = 300;

    private const LIST_CACHE_KEYS_INDEX = 'materi.list.keys';

    public function list(string $role, int $page = 1, int $perPage = self::DEFAULT_PER_PAGE): MateriListData
    {
        $cacheKey = $this->listCacheKey(
            role: $role,
            page: $page,
        );

        $cachedPayload = Cache::remember(
            key: $cacheKey,
            ttl: self::LIST_CACHE_TTL_SECONDS,
            callback: function () use ($cacheKey, $perPage, $page): array {
                $this->rememberListCacheKey($cacheKey);

                $paginator = FileMateri::query()
                    ->latest()
                    ->paginate(
                        perPage: $perPage,
                        columns: ['*'],
                        pageName: 'page',
                        page: $page,
                    );

                // Cache array, not DTO object, to avoid __PHP_Incomplete_Class after class/autoload changes.
                return $this->materiListDataToArray($this->toListData($paginator));
            },
        );

        if (! is_array($cachedPayload)) {
            Cache::forget($cacheKey);

            return $this->list(
                role: $role,
                page: $page,
                perPage: $perPage,
            );
        }

        return $this->materiListDataFromArray($cachedPayload);
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

            $this->invalidateListCache();

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

            $this->invalidateListCache();

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

            $this->invalidateListCache();
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
            ->map(static fn(FileMateri $materi): MateriData => MateriData::fromModel($materi));

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

    private function listCacheKey(string $role, int $page): string
    {
        return "materi.list.{$role}.{$page}";
    }

    private function rememberListCacheKey(string $cacheKey): void
    {
        $keys = Cache::get(self::LIST_CACHE_KEYS_INDEX, []);

        if (! is_array($keys)) {
            $keys = [];
        }

        $keys[] = $cacheKey;

        Cache::forever(
            key: self::LIST_CACHE_KEYS_INDEX,
            value: array_values(array_unique($keys)),
        );
    }

    private function invalidateListCache(): void
    {
        $keys = Cache::get(self::LIST_CACHE_KEYS_INDEX, []);

        if (! is_array($keys)) {
            Cache::forget(self::LIST_CACHE_KEYS_INDEX);

            return;
        }

        foreach ($keys as $key) {
            if (is_string($key)) {
                Cache::forget($key);
            }
        }

        Cache::forget(self::LIST_CACHE_KEYS_INDEX);
    }

    /**
     * @return array{
     *     items: list<array{
     *         id: int,
     *         title: string,
     *         description: string,
     *         type: string,
     *         file_path: string|null,
     *         file_url: string|null,
     *         youtube_url: string|null,
     *         youtube_embed_url: string|null,
     *         created_by: int,
     *         created_at: string,
     *         updated_at: string
     *     }>,
     *     meta: array<string, int>
     * }
     */
    private function materiListDataToArray(MateriListData $materiListData): array
    {
        return [
            'items' => $materiListData->items
                ->map(fn(MateriData $materi): array => $this->materiDataToArray($materi))
                ->values()
                ->all(),
            'meta' => $materiListData->meta,
        ];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function materiListDataFromArray(array $payload): MateriListData
    {
        $items = collect($payload['items'] ?? [])
            ->filter(fn(mixed $item): bool => is_array($item))
            ->map(fn(array $item): MateriData => $this->materiDataFromArray($item))
            ->values();

        $meta = $payload['meta'] ?? [];

        return new MateriListData(
            items: $items,
            meta: is_array($meta) ? $meta : [],
        );
    }

    /**
     * @return array{
     *     id: int,
     *     title: string,
     *     description: string,
     *     type: string,
     *     file_path: string|null,
     *     file_url: string|null,
     *     youtube_url: string|null,
     *     youtube_embed_url: string|null,
     *     created_by: int,
     *     created_at: string,
     *     updated_at: string
     * }
     */
    private function materiDataToArray(MateriData $materi): array
    {
        return [
            'id' => $materi->id,
            'title' => $materi->title,
            'description' => $materi->description,
            'type' => $materi->type->value,
            'file_path' => $materi->filePath,
            'file_url' => $materi->fileUrl,
            'youtube_url' => $materi->youtubeUrl,
            'youtube_embed_url' => $materi->youtubeEmbedUrl,
            'created_by' => $materi->createdBy,
            'created_at' => $materi->createdAt,
            'updated_at' => $materi->updatedAt,
        ];
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function materiDataFromArray(array $payload): MateriData
    {
        return new MateriData(
            id: (int) $payload['id'],
            title: (string) $payload['title'],
            description: (string) $payload['description'],
            type: MateriType::from((string) $payload['type']),
            filePath: $payload['file_path'] ?? null,
            fileUrl: $payload['file_url'] ?? null,
            youtubeUrl: $payload['youtube_url'] ?? null,
            youtubeEmbedUrl: $payload['youtube_embed_url'] ?? null,
            createdBy: (int) $payload['created_by'],
            createdAt: (string) $payload['created_at'],
            updatedAt: (string) $payload['updated_at'],
        );
    }
}
