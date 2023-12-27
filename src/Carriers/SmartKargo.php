<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class SmartKargo extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.smart_kargo.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.smartkargo.com/company/contact-2/';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.smart_kargo.signup_text');
    }

    protected function image(): string
    {
        return 'smartkargo-logo.1e53d92f91028944a956559ff64a32d9.svg';
    }
}
