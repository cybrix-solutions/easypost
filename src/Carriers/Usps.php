<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Usps extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.usps.name');
    }

    public function nameForTracker(): string
    {
        return 'USPS';
    }

    public function voidableDays(): int
    {
        return config('easypost.voidable_days.usps', 30);
    }

    public function dailyRateDivisor(): int|float
    {
        return 166;
    }

    protected function image(): string
    {
        return 'usps-logo.ec465bb0e3d017c6055dd1aa86234e41.png';
    }
}
