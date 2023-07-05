<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Maergo extends Carrier
{
    protected function image(): string
    {
        return 'maergo-logo.19e98070c5e2a3c8defc456612ae00c7.svg';
    }

    public function name(): string
    {
        return __('easypost::carriers.maergo.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://maergo.com/get-in-touch/';
    }
}
