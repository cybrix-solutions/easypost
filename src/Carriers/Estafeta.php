<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class Estafeta extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.estafeta.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://estafetausa.com/contact-us/';
    }

    protected function image(): string
    {
        return 'estafeta-logo-ca.f746f64ef5ff16c8fd52ddc53b274ed6.png';
    }
}
