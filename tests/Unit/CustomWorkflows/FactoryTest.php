<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\CustomWorkflows\Factory;
use CybrixSolutions\EasyPost\CustomWorkflows\FedexWorkflow;
use CybrixSolutions\EasyPost\CustomWorkflows\UpsWorkflow;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Exceptions\InvalidCarrierForCustomWorkflow;
use CybrixSolutions\EasyPost\Services\CarrierService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierTypesMock;

beforeEach(function () {
    mockProductionApi([
        CarrierTypesMock::make(),
    ]);
});

it('can resolve a custom workflow', function (CarrierEnum $enum, string $expectedWorkflow) {
    $carrierService = CarrierService::fromType($enum);

    $workflow = Factory::make($carrierService);

    expect($workflow)->toBeInstanceOf($expectedWorkflow);
})->with([
    [CarrierEnum::Ups, UpsWorkflow::class],
    [CarrierEnum::Fedex, FedexWorkflow::class],
]);

it('throws an exception for unsupported carrier types', function () {
    $carrierService = CarrierService::fromType(CarrierEnum::Speedee);

    Factory::make($carrierService);
})->throws(InvalidCarrierForCustomWorkflow::class);
