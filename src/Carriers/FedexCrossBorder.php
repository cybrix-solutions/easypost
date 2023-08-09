<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class FedexCrossBorder extends Carrier
{
    protected function image(): string
    {
        return 'fedex-cross-border-logo.fbe337d854fa22aada60a4565a78afb6.png';
    }

    public function name(): string
    {
        return __('easypost::carriers.fedex_cross_border.name');
    }

    public function nameForTracker(): string
    {
        return 'FedExCrossBorder';
    }

    public function signupUrl(): ?string
    {
        return 'https://crossborder.fedex.com/us/';
    }

    public function optionsFor(string $field): array
    {
        if ($field !== 'origin_hub') {
            return [];
        }

        return [
            'Fontana' => 'Fontana',
            'Saddle Brook' => 'Saddle Brook',
        ];
    }
}
