<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class ColumbusLastMile extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.cdl.name');
    }

    public function signupInstructions(): ?string
    {
        return __('easypost::carriers.cdl.signup_instructions');
    }

    protected function image(): string
    {
        return 'cdl-logo.36394afca70e0e663858c364798b6008.png';
    }
}
