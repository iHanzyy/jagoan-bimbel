<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\MateriType;
use App\Models\FileMateri;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

final readonly class MateriData
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public MateriType $type,
        public ?string $filePath,
        public ?string $fileUrl,
        public ?string $youtubeUrl,
        public ?string $youtubeEmbedUrl,
        public int $createdBy,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }

    public static function fromModel(FileMateri $materi): self
    {
        return new self(
            id: (int) $materi->id,
            title: (string) $materi->title,
            description: (string) $materi->description,
            type: $materi->type,
            filePath: $materi->file_path,
            fileUrl: self::resolveFileUrl($materi),
            youtubeUrl: $materi->youtube_url,
            youtubeEmbedUrl: self::resolveYoutubeEmbedUrl($materi->youtube_url),
            createdBy: (int) $materi->created_by,
            createdAt: $materi->created_at?->toISOString() ?? '',
            updatedAt: $materi->updated_at?->toISOString() ?? '',
        );
    }

    private static function resolveFileUrl(FileMateri $materi): ?string
    {
        if ($materi->file_path === null) {
            return null;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        return $disk->url($materi->file_path);
    }

    private static function resolveYoutubeEmbedUrl(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }

        $videoId = self::extractYoutubeVideoId($url);

        return $videoId === null
            ? null
            : "https://www.youtube.com/embed/{$videoId}";
    }

    private static function extractYoutubeVideoId(string $url): ?string
    {
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches) === 1) {
                return $matches[1];
            }
        }

        return null;
    }
}
