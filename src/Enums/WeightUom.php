<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums;

enum WeightUom: string
{
    case Ounce = 'oz';
    case Pound = 'lb';
    case Gram = 'g';
    case Kilogram = 'kg';

    public function toOunces(float $weight): float
    {
        return ceil(match ($this) {
            self::Ounce => $weight,
            self::Pound => $weight * 16,
            self::Gram => $weight / 28.35,
            self::Kilogram => $weight * 35.274,
        } * 100) / 100;
    }

    public function toPounds(float $weight): float
    {
        return ceil(match ($this) {
            self::Ounce => $weight / 16,
            self::Pound => $weight,
            self::Gram => $weight / 453.6,
            self::Kilogram => $weight * 2.205,
        } * 100) / 100;
    }
}
