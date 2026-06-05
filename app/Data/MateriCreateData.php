<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\MateriType;
use Illuminate\Http\UploadedFile;

final readonly class MateriCreateData
{
    public function __construct(
        public string $title,
        public string $description,
        public MateriType $type,
        public ?UploadedFile $file,
        public ?string $youtubeUrl,
        public int $createdBy,
    ) {
    }
}
