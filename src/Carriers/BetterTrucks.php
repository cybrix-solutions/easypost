<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class BetterTrucks extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.better_trucks.name');
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.better_trucks.signup_text');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.bettertrucks.com/contact/';
    }

    protected function image(): string
    {
        return 'better-trucks-logo.a427dbe22697a5608701813a5d6c52c9.png';
    }
}
