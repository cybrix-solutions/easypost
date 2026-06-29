<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Services\Api\EasyPostClient;
use CybrixSolutions\EasyPost\Services\Api\ProductionEasyPostClient;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use CybrixSolutions\EasyPost\Tests\TestCase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\View;

uses(TestCase::class)->in(__DIR__);

uses(InteractsWithViews::class)->beforeEach(function () {
    $this->markTestSkipped('Deprecated Blade view component tests are disabled; carrier account UI is covered by Filament components.');

    View::addLocation(__DIR__ . '/resources/views');
})->in(__DIR__ . '/Unit/ViewComponents');

// Helpers

function mockApi(array $mocks): void
{
    $api = app(EasyPostClient::class);

    foreach ($mocks as $mock) {
        $api->addMock($mock);
    }

    $api->mock();
}

function mockProductionApi(array $mocks): void
{
    if (blank(config('easypost.api_key'))) {
        config()->set('easypost.api_key', 'production_api_key');
    }

    $api = app(ProductionEasyPostClient::class);

    foreach ($mocks as $mock) {
        $api->addMock($mock);
    }

    $api->mock();
}

function mockWebhookApi(array $productionMocks = [], array $testMocks = []): void
{
    $api = app(WebhooksService::class);

    if ($testMocks !== [] && blank(config('easypost.test_api_key'))) {
        config()->set('easypost.test_api_key', 'test_api_key');
    }

    foreach ($productionMocks as $mock) {
        $api->addProductionMock($mock);
    }

    foreach ($testMocks as $mock) {
        $api->addTestMock($mock);
    }
}
