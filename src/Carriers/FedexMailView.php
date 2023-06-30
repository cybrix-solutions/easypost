<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class FedexMailView extends Carrier
{
    protected function image(): string
    {
        return 'fedex-logo.380cc4d39a40f4623dceaad6936f9d4d.svg';
    }

    public function name(): string
    {
        return __('easypost::carriers.fedex_mail_view.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.fedex.com/en-us/shipping/international-mail-service.html';
    }
}
