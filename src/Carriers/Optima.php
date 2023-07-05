<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Optima extends Carrier
{
    protected function image(): string
    {
        return 'optima-logo.3f5bf5d22f69129b95cc341e8e4b64b9.svg';
    }

    public function name(): string
    {
        return __('easypost::carriers.optima.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://shipoptima.com/';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.optima.signup_text');
    }
}
