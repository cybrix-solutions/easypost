<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums\ShipmentOptions;

/**
 * @see https://www.easypost.com/docs/api/php#options
 */
enum LabelFormat: string
{
    case Png = 'PNG';
    case Pdf = 'PDF';
    case Zpl = 'ZPL';
    case Epl2 = 'EPL2';

    public static function values(): array
    {
        return array_map(
            fn (self $format) => $format->value,
            self::cases(),
        );
    }

    public static function isSupported(string $format): bool
    {
        $instance = self::tryFrom(strtoupper($format));

        return $instance !== null;
    }
}
