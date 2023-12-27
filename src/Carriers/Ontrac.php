<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Ontrac extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.ontrac.name');
    }

    public function nameForTracker(): string
    {
        return 'OnTrac';
    }

    public function signupUrl(): ?string
    {
        return 'https://www.ontrac.com/openfreeaccount.asp';
    }

    protected function image(): string
    {
        return 'ontrac-logo.4e5c272dd60b773b4f0bbe019f46c2a1.jpg';
    }
}
