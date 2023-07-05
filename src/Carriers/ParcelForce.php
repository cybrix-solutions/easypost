<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class ParcelForce extends Carrier
{
    protected function image(): string
    {
        return 'parcel-force-logo-ca.7c11038bb33d37970003ab4a4ef2c9f2.svg';
    }

    public function name(): string
    {
        return __('easypost::carriers.parcel_force.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.parcelforce.com/user/register';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.parcel_force.signup_text');
    }
}
