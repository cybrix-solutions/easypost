<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Apc extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.apc.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.apc-pli.com/contact.html';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.apc.signup_text');
    }

    protected function image(): string
    {
        return 'apc-logo-ca.d435388f7592488d348c469ace5c7adb.png';
    }
}
