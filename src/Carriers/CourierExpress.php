<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class CourierExpress extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.courier_express.name');
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.courier_express.signup_text');
    }

    public function signupUrl(): ?string
    {
        return 'https://courierexpress.net/contact-us';
    }

    protected function image(): string
    {
        return 'courier-express-logo.9212d96981cefe2c34eafb1f998d4049.png';
    }
}
