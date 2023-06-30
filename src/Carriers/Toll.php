<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Toll extends Carrier
{
    protected function image(): string
    {
        return 'toll-logo.f9b3a3508d78721b4bbafadc617159ec.svg';
    }

    public function name(): string
    {
        return __('easypost::carriers.toll.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.tollgroup.com/contact-us';
    }
}
