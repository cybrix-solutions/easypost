<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class DhlEcs extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.dhl_ecs.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.dhl.com/global-en/home/our-divisions/ecommerce-solutions/customer-service.html';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.dhl_ecs.signup_text');
    }

    protected function image(): string
    {
        return 'dhl-logo.f97c38914dd385846512f7ed16a275a3.svg';
    }
}
