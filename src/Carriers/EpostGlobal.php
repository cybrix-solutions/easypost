<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class EpostGlobal extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.epost_global.name');
    }

    public function nameForTracker(): string
    {
        return 'ePostGlobal';
    }

    public function signupUrl(): ?string
    {
        return 'https://epostglobalshipping.com/contact-us/';
    }

    protected function image(): string
    {
        return 'epost-global-logo.5efbc590386ed0a79f63f672a850b864.png';
    }
}
