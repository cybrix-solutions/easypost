<?php

use CybrixSolutions\EasyPost\Services\Api\ProductionEasyPostClient;
use CybrixSolutions\EasyPost\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

// Helpers

function mockProductionApi(array $mocks): void
{
    $api = app(ProductionEasyPostClient::class);

    foreach ($mocks as $mock) {
        $api->addMock($mock);
    }

    $api->mock();
}
