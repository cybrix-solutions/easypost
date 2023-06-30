<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Services\Api\ProductionEasyPostClient;
use EasyPost\EasyPostObject;

beforeEach(function () {
    $this->api = app(ProductionEasyPostClient::class);
});

it('can get a list of available carriers', function () {
    $types = $this->api->carrierAccount->types();

    expect($types)->toBeArray()
        ->and($types)->not->toBeEmpty()
        ->and($types)->toContainOnlyInstancesOf(EasyPostObject::class);
});
