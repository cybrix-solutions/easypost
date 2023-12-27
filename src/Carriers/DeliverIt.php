<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class DeliverIt extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.deliver_it.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://deliverit.e-courier.com/deliverit/home/help-Request.asp?UserGUID=&Opt=Reg';
    }

    protected function image(): string
    {
        return 'deliver-it-logo.4f3c8689345fd6d31038c0efacb9eac0.svg';
    }
}
