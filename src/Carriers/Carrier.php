<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\Carrier as CarrierContract;

abstract readonly class Carrier implements CarrierContract
{
    abstract protected function image(): string;

    public function nameForTracker(): string
    {
        return class_basename($this);
    }

    public function imageUrl(): string
    {
        return asset("vendor/easypost/images/carriers/{$this->image()}");
    }

    public function companyField(): string
    {
        return 'company';
    }

    public function nameField(): string
    {
        return 'name';
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

    /**
     * The divisor used for calculating dimensional weights for a carrier.
     */
    public function dailyRateDivisor(): int|float
    {
        return 139;
    }

    public function maxRefNumberLength(): int
    {
        return 30;
    }
}
