<?php

declare(strict_types=1);

namespace App\Data;

final readonly class DownloadableMateriData
{
    public function __construct(
        public string $disk,
        public string $path,
        public string $downloadName,
    ) {
    }
}
