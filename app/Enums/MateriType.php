<?php

declare(strict_types=1);

namespace App\Enums;

enum MateriType: string
{
    case Pdf = 'pdf';
    case Image = 'image';
    case Youtube = 'youtube';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(
            callback: static fn (self $type): string => $type->value,
            array: self::cases(),
        );
    }

    public function isFileBased(): bool
    {
        return match ($this) {
            self::Pdf, self::Image => true,
            self::Youtube => false,
        };
    }

    public function isYoutube(): bool
    {
        return $this === self::Youtube;
    }

    public function storageDirectory(): string
    {
        return match ($this) {
            self::Pdf => 'materis/pdf',
            self::Image => 'materis/image',
            self::Youtube => 'materis/youtube',
        };
    }

    /**
     * @return list<string>
     */
    public function acceptedMimeTypes(): array
    {
        return match ($this) {
            self::Pdf => ['application/pdf'],
            self::Image => ['image/jpeg', 'image/png'],
            self::Youtube => [],
        };
    }
}
