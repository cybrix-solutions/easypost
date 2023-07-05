<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class DaiPost extends Carrier
{
    protected function image(): string
    {
        return 'dai-post-logo.b75bb45fde80c8be055eb9d57a4ad0bb.png';
    }

    public function name(): string
    {
        return __('easypost::carriers.dai_post.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.daipost.com/#contact';
    }
}
