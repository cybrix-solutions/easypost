<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Veho extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.veho.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://shipveho.com/solutions/';
    }

    protected function image(): string
    {
        return 'veho-logo.2ee53da40293ba70af93d13a352e2a54.png';
    }
}
