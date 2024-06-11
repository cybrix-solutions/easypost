<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Flexport extends Carrier
{
    protected function image(): string
    {
        return 'flexport-logo.d45785223feae54a7915bf2e3a50a191.png';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.flexport.signup_text');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.flexport.com/sign-up/';
    }

    public function name(): string
    {
        return __('easypost::carriers.flexport.name');
    }
}
