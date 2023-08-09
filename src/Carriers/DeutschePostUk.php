<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class DeutschePostUk extends Carrier
{
    protected function image(): string
    {
        return 'deutsche-post-logo-ca.691a2de2a228b486e2b98ee88b4d313e.png';
    }

    public function name(): string
    {
        return __('easypost::carriers.deutsche_post_uk.name');
    }

    public function nameForTracker(): string
    {
        return 'DeutschePostUK';
    }

    public function signupInstructions(): ?string
    {
        return __('easypost::carriers.deutsche_post_uk.signup_instructions');
    }
}
