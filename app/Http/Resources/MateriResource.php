<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Data\MateriData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use RuntimeException;

final class MateriResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $materi = $this->materiData();

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

    private function materiData(): MateriData
    {
        if (! $this->resource instanceof MateriData) {
            throw new RuntimeException('MateriResource expects MateriData.');
        }

        return $this->resource;
    }
}
