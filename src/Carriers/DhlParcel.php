<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class DhlParcel extends Carrier
{
    protected function image(): string
    {
        return 'dhl-logo.f97c38914dd385846512f7ed16a275a3.svg';
    }

    public function name(): string
    {
        return __('easypost::carriers.dhl_parcel.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://developer.dhl.com/api-reference/parcel-eu#get-started-section/';
    }
}
