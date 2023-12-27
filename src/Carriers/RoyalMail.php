<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class RoyalMail extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.royal_mail.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.royalmail.com/register';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.royal_mail.signup_text');
    }

    protected function image(): string
    {
        return 'royal-mail-logo.1a8661a9cdada8be94ee6796a9d7fbbe.png';
    }
}
