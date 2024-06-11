<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Gso extends Carrier
{
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

    protected function image(): string
    {
        return 'gls-logo-ca.bb1180185869bbe9578db66acee67a40.png';
    }
}
