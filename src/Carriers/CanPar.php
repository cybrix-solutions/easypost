<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class CanPar extends Carrier
{
    protected function image(): string
    {
        return 'canpar-logo.7b3491f751e9bb129fab1759874ca29e.png';
    }

    public function name(): string
    {
        return __('easypost::carriers.canpar.name');
    }

    public function nameForTracker(): string
    {
        return 'Canpar';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.canpar.signup_text');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.canpar.com/en/contact/become_customer.htm';
    }
}
