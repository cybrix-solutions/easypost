<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class PassportGlobal extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.passport_global.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://passportshipping.com/contact-sales';
    }

    protected function image(): string
    {
        return 'passport-logo.5fbd27d5e0667703f60395e1b12596ee.png';
    }
}
