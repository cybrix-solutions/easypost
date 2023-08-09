<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\CarrierAccounts;

interface Carrier
{
    public function imageUrl(): string;

    public function name(): string;

    public function nameForTracker(): string;

    public function companyField(): string;

    public function nameField(): string;

    public function signupHelpUrl(): ?string;

    public function signupInstructions(): ?string;

    public function signupText(): ?string;

    public function signupUrl(): ?string;

    public function voidableDays(): int;

    public function needsTermsAccepted(): bool;

    public function optionsFor(string $field): array;

    public function dailyRateDivisor(): int|float;

    public function maxRefNumberLength(): int;
}
