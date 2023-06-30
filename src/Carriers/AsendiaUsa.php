<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class AsendiaUsa extends Carrier
{
    protected function image(): string
    {
        return 'asendia-logo.d2766b26a9074ba9b5513bafaa24218a.png';
    }

    public function name(): string
    {
        return __('easypost::carriers.asendia_usa.name');
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.asendia_usa.signup_text');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.asendiausa.com/contact/sales';
    }

    public function optionsFor(string $field): array
    {
        if ($field !== 'carrier_facility') {
            return [];
        }

        return [
            'SFO' => 'SFO',
            'MIA' => 'MIA',
            'JFK' => 'JFK',
            'PHL' => 'PHL',
            'ORD' => 'ORD',
            'LAX' => 'LAX',
            'SLC' => 'SLC',
            'TOR' => 'TOR',
        ];
    }
}
