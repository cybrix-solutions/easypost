<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Purolator extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.purolator.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://eshiponline.purolator.com/ShipOnline/SecurePages/Public/Register.aspx?lang=E';
    }

    protected function image(): string
    {
        return 'purolator-logo-ca.73c3c59cd56e23db30be2480678431e4.png';
    }
}
