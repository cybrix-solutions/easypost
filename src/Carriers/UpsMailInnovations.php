<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class UpsMailInnovations extends Carrier
{
    protected function image(): string
    {
        return 'ups-mail-innovations-logo-ca.f894c0d2660078a054573d9df29790dd.png';
    }

    public function name(): string
    {
        return __('easypost::carriers.ups_mail_innovations.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.ups.com/us/en/supplychain/solutions/mail-innovations.page';
    }
}
