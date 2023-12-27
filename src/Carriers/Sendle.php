<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Sendle extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.sendle.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.sendle.com/users/sign_up';
    }

    protected function image(): string
    {
        return 'sendle-logo.2112de55ec0aed7ebbd27204abbc2b25.svg';
    }
}
