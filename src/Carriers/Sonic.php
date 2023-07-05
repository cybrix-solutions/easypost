<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Sonic extends Carrier
{
    protected function image(): string
    {
        return 'sonic-logo.c34f4d920d54a72f5b4a290170918381.svg';
    }

    public function name(): string
    {
        return __('easypost::carriers.sonic.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://00410.xdhosted.com/rapidship/#/register';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.create_account');
    }
}
