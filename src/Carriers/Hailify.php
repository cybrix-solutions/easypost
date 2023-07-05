<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Hailify extends Carrier
{
    protected function image(): string
    {
        return 'hailify-logo.f081882605753164ae749e556f2aa610.svg';
    }

    public function name(): string
    {
        return __('easypost::carriers.hailify.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.drivehailify.com/';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.hailify.signup_text');
    }
}
