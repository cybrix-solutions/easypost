<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\CustomWorkflows\Factory;
use CybrixSolutions\EasyPost\CustomWorkflows\FedexWorkflow;
use CybrixSolutions\EasyPost\CustomWorkflows\UpsWorkflow;
use CybrixSolutions\EasyPost\Exceptions\InvalidCarrierForCustomWorkflow;
use CybrixSolutions\EasyPost\Services\CarrierService;
use CybrixSolutions\EasyPost\Tests\Fixtures\Responses\Carriers\CarrierResponses;
use EasyPost\EasyPostObject;

it('can resolve a custom workflow', function (EasyPostObject $carrierResponse, string $expectedWorkflow) {
    $carrierService = new CarrierService($carrierResponse);

    $workflow = Factory::make($carrierService);

    expect($workflow)->toBeInstanceOf($expectedWorkflow);
})->with([
    [fn () => CarrierResponses::upsAccount(), UpsWorkflow::class],
    [fn () => CarrierResponses::fedexAccount(), FedexWorkflow::class],
]);

it('throws an exception for unsupported carrier types', function () {
    $carrierService = new CarrierService(CarrierResponses::speedeeAccount());

    Factory::make($carrierService);
})->throws(InvalidCarrierForCustomWorkflow::class);
