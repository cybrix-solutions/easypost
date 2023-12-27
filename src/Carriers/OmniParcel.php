<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class OmniParcel extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.omni_parcel.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.sekologistics.com/us/contact/';
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.omni_parcel.signup_text');
    }

    protected function image(): string
    {
        return 'omniparcel-logo.fc4edd5bc861e8b27449552be890cc96.png';
    }
}
