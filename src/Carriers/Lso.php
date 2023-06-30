<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Lso extends Carrier
{
    protected function image(): string
    {
        return 'lso-logo.ca554d5ffe22627660daff74ffd237a1.jpg';
    }

    public function name(): string
    {
        return __('easypost::carriers.lso.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.lso.com/create-account';
    }

    public function companyField(): string
    {
        return 'attention';
    }

    public function nameField(): string
    {
        return 'name';
    }
}
