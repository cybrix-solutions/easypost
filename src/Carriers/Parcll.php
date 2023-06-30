<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Parcll extends Carrier
{
    protected function image(): string
    {
        return 'parcll-logo.c40efed65644f498f8712fe6f4c6ad1e.png';
    }

    public function name(): string
    {
        return __('easypost::carriers.parcll.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.parcll.com/contact-us';
    }

    public function optionsFor(string $field): array
    {
        if ($field !== 'origin_hub') {
            return [];
        }

        return [
            'ES' => 'East',
            'WE' => 'West',
            'CE' => 'Central',
            'NE' => 'Northeast',
            'SE' => 'Southeast',
            'SO' => 'South',
        ];
    }
}
