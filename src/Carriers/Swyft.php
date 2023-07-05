<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Swyft extends Carrier
{
    protected function image(): string
    {
        return 'swyft-logo.9411caf8231ddced8a20c397265ac351.svg';
    }

    public function name(): string
    {
        return __('easypost::carriers.swyft.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.useswyft.com/contact';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.swyft.signup_text');
    }
}
