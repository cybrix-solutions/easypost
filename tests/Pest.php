<?php

use CybrixSolutions\EasyPost\Services\Api\ProductionEasyPostClient;
use CybrixSolutions\EasyPost\Tests\TestCase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\View;

uses(TestCase::class)->in(__DIR__);

uses(InteractsWithViews::class)->beforeEach(function () {
    View::addLocation(__DIR__ . '/resources/views');
})->in(__DIR__ . '/Unit/ViewComponents');

// Helpers

function mockProductionApi(array $mocks): void
{
    $api = app(ProductionEasyPostClient::class);

    foreach ($mocks as $mock) {
        $api->addMock($mock);
    }

    $api->mock();
}
