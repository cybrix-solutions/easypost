<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Dpd extends Carrier
{
    protected function image(): string
    {
        return 'dpd-logo.b01f5a2d7f0899264a7535434d5ad4d9.svg';
    }

    public function name(): string
    {
        return __('easypost::carriers.dpd.name');
    }
}
