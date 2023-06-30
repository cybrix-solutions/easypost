<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class LoomisExpress extends Carrier
{
    protected function image(): string
    {
        return 'loomis-express-ca.c3c87fae3bac5a05e49003dda1191676.gif';
    }

    public function name(): string
    {
        return __('easypost::carriers.loomis_express.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.loomisexpress.com/loomship/MyAccount/SignUpProfileAccount';
    }
}
