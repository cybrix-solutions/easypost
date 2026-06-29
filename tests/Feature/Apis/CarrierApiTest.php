<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Services\Api\ProductionEasyPostClient;
use EasyPost\EasyPostObject;

beforeEach(function () {
    $this->api = app(ProductionEasyPostClient::class);
});

it('can get a list of available carriers', function () {
    if (! filter_var(env('EASYPOST_RUN_LIVE_API_TESTS', false), FILTER_VALIDATE_BOOLEAN)) {
        $this->markTestSkipped('Live EasyPost API tests are disabled.');
    }

    $types = $this->api->carrierAccount->types();

    expect($types)->toBeArray()
        ->and($types)->not->toBeEmpty()
        ->and($types)->toContainOnlyInstancesOf(EasyPostObject::class);
});
