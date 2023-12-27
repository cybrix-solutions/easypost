<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class DhlExpress extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.dhl_express.name');
    }

    public function nameForTracker(): string
    {
        return 'DHLExpress';
    }

    public function signupUrl(): ?string
    {
        return 'https://mydhl.express.dhl/gb/en/forms/open-account.html';
    }

    protected function image(): string
    {
        return 'dhl-express.765b5da0b895eccb232fcf680c81d1d9.png';
    }
}
