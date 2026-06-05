<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Support\Collection;

final readonly class MateriListData
{
    /**
     * @param Collection<int, MateriData> $items
     * @param array<string, int> $meta
     */
    public function __construct(
        public Collection $items,
        public array $meta,
    ) {
    }
}
