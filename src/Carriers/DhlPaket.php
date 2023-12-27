<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class DhlPaket extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.dhl_paket.name');
    }

    public function nameForTracker(): string
    {
        return 'DHLPaket';
    }

    public function signupUrl(): ?string
    {
        return 'https://www.dhl.de/en/privatkunden/kundenkonto/registrierung.html';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.create_account');
    }

    protected function image(): string
    {
        return 'dhl-paket-logo.36164d4d5647dafca07e025965c77f6f.svg';
    }
}
