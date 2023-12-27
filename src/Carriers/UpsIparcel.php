<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class UpsIparcel extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.ups_i_parcel.name');
    }

    public function nameForTracker(): string
    {
        return 'UPSIparcel';
    }

    protected function image(): string
    {
        return 'ups-i-parcel-logo.98a50e9196cb84389b8df1b77b29dacc.jpg';
    }
}
