<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class AxleHireV3 extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.axlehire_v3.name');
    }

    public function nameForTracker(): string
    {
        return 'AxlehireV3';
    }

    public function signupInstructions(): ?string
    {
        return __('easypost::carriers.axlehire_v3.signup_instructions');
    }

    protected function image(): string
    {
        return 'axlehire-logo.3db08a344ca8685d194d3ae78c419148.png';
    }
}
