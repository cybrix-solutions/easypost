<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class SfExpress extends Carrier
{
    protected function image(): string
    {
        return 'sf-express-logo.40e7d031ace5f1f64893d48d461f2866.png';
    }

    public function name(): string
    {
        return __('easypost::carriers.sf_express.name');
    }

    public function nameForTracker(): string
    {
        return 'SFExpress';
    }

    public function signupUrl(): ?string
    {
        return 'https://www.sf-express.com/chn/en';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.create_account');
    }
}
