<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class DhlEcommerceAsia extends Carrier
{
    protected function image(): string
    {
        return 'dhl-logo.f97c38914dd385846512f7ed16a275a3.svg';
    }

    public function signupUrl(): ?string
    {
        return 'https://www.dhl.com/global-en/home/our-divisions/ecommerce-solutions/customer-service.html';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.dhl_ecommerce_asia.signup_text');
    }

    public function name(): string
    {
        return __('easypost::carriers.dhl_ecommerce_asia.name');
    }
}
