<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Tforce extends Carrier
{
    protected function image(): string
    {
        return 'tforce-logistics-logo.5b4e4ca4f530c0a3eae40c3a71d5451d.png';
    }

    public function name(): string
    {
        return __('easypost::carriers.tforce_concise.name');
    }

    public function nameForTracker(): string
    {
        return 'TForce';
    }

    public function signupUrl(): ?string
    {
        return 'https://www.tforcelogistics.com/service/e-commerce/';
    }
}
