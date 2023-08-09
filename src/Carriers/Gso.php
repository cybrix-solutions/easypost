<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Gso extends Carrier
{
    protected function image(): string
    {
        return 'gso-logo.12057833129efa6432d8d08799dffab1.png';
    }

    public function name(): string
    {
        return __('easypost::carriers.gso.name');
    }

    public function nameForTracker(): string
    {
        return 'GSO';
    }

    public function signupUrl(): ?string
    {
        return 'https://www.gls-us.com/create-account';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.gso.signup_text');
    }
}
