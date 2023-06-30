<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

use CybrixSolutions\EasyPost\Contracts\Carrier as CarrierContract;

abstract readonly class Carrier implements CarrierContract
{
    abstract protected function image(): string;

    public function imageUrl(): string
    {
        return asset("vendor/easypost/images/carriers/{$this->image()}");
    }

    public function companyField(): string
    {
        return 'name';
    }

    public function nameField(): string
    {
        return 'attention';
    }

    public function signupHelpUrl(): ?string
    {
        return null;
    }

    public function signupInstructions(): ?string
    {
        return null;
    }

    public function signupUrl(): ?string
    {
        return null;
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.request_account');
    }

    public function voidableDays(): int
    {
        return config('easypost.voidable_days.default', 90);
    }

    public function needsTermsAccepted(): bool
    {
        return false;
    }

    public function optionsFor(string $field): array
    {
        return [];
    }
}
