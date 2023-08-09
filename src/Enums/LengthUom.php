<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums;

enum LengthUom: string
{
    case Inch = 'in';
    case Centimeter = 'cm';
    case Foot = 'ft';
    case Millimeter = 'mm';

    public function toInches(float $length): float
    {
        return ceil(match ($this) {
            self::Inch => $length,
            self::Centimeter => $length / 2.54,
            self::Foot => $length * 12,
            self::Millimeter => $length / 25.4,
        } * 100) / 100;
    }
}
