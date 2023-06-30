<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Speedee extends Carrier
{
    protected function image(): string
    {
        return 'speedee-logo-ca.f992e721f20b79a7631589fd1889ddc1.png';
    }

    public function name(): string
    {
        return __('easypost::carriers.speedee.name');
    }

    public function signupInstructions(): ?string
    {
        return __('easypost::carriers.speedee.signup_instructions');
    }
}
