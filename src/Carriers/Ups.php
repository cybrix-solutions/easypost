<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Ups extends Carrier
{
    protected function image(): string
    {
        return 'ups-logo.28ef2757acc2c805a47f22be998e9222.svg';
    }

    public function name(): string
    {
        return __('easypost::carriers.ups.name');
    }

    public function signupHelpUrl(): ?string
    {
        return 'https://support.easypost.com/hc/en-us/articles/360024355712-Setting-up-your-UPS-Account';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.create_account');
    }

    public function signupUrl(): ?string
    {
        return 'https://wwwapps.ups.com/doapp/signup?loc=en_US';
    }

    public function needsTermsAccepted(): bool
    {
        return true;
    }
}
