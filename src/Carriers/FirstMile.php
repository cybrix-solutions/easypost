<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class FirstMile extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.first_mile.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.firstmile.com/get-a-quote';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.first_mile.signup_text');
    }

    protected function image(): string
    {
        return 'firstmile-logo.aec8303bc035553c900c993899b84c80.png';
    }
}
