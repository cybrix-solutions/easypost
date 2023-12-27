<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Uds extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.uds.name');
    }

    public function nameForTracker(): string
    {
        return 'UDS';
    }

    public function signupUrl(): ?string
    {
        return 'https://www.uniteddeliveryservice.com/request-a-quote.html';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.uds.signup_text');
    }

    protected function image(): string
    {
        return 'uds-logo-ca.7084a8633a4cea4d4ca9977ad208fcab.png';
    }
}
