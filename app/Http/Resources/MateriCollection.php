<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Data\MateriListData;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use RuntimeException;

final class MateriCollection extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $materiList = $this->materiListData();

        return [
            'data' => MateriResource::collection($materiList->items)->resolve($request),
            'meta' => $materiList->meta,
            'message' => 'Daftar materi berhasil diambil.',
        ];
    }

    private function materiListData(): MateriListData
    {
        if (! $this->resource instanceof MateriListData) {
            throw new RuntimeException('MateriCollection expects MateriListData.');
        }

        return $this->resource;
    }
}
