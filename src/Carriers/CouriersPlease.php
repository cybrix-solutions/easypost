<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class CouriersPlease extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.couriers_please.name');
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.create_account');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.couriersplease.com.au/User-Registration';
    }

    protected function image(): string
    {
        return 'couriersplease-logo.648fd0bc62479e42830d75a873f3dfb5.jpeg';
    }
}
