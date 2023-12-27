<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class AustraliaPost extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.australia_post.name');
    }

    public function signupText(): ?string
    {
        return __('easypost::carriers.create_account');
    }

    public function signupUrl(): ?string
    {
        return 'https://auspost.com.au/mypost-business/auth/email';
    }

    protected function image(): string
    {
        return 'australia-post.624d39e3aa7b6d497dc997fceb3dfeac.svg';
    }
}
