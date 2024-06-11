<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Fastway extends Carrier
{
    protected function image(): string
    {
        return 'fastway-logo-ca.94055ac99e00cc1feea29b20bf744f4e.png';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.fastway.signup_text');
    }

    public function signupUrl(): ?string
    {
        return 'https://sa.api.fastway.org/v3/docs/page/GetAPIKey.html';
    }

    public function name(): string
    {
        return __('easypost::carriers.fastway.name');
    }
}
