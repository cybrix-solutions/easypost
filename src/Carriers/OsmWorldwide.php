<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Carriers;

final readonly class OsmWorldwide extends Carrier
{
    public function name(): string
    {
        return __('easypost::carriers.osm_worldwide.name');
    }

    public function signupUrl(): ?string
    {
        return 'https://www.osmworldwide.com/contact/request-a-quote/';
    }

    protected function image(): string
    {
        return 'osm-worldwide-logo.9751fde3890274e08b01e3b4671aa022.jpg';
    }
}
