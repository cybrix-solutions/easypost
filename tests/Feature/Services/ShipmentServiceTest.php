<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Exceptions\Shipments\ShipmentCreationFailed;
use CybrixSolutions\EasyPost\Exceptions\Shipments\ShipmentRetrievalFailed;
use CybrixSolutions\EasyPost\Services\ShipmentService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Shipments\ShipmentMock;
use EasyPost\Shipment;

beforeEach(function () {
    $this->api = app(ShipmentService::class);
});

it('can create a shipment when the carrier returns a rate', function () {
    mockApi([
        ShipmentMock::make()
            ->forCreation()
            ->withRate(),
    ]);

    $shipment = $this->api->create([]);

    expect($shipment)
        ->toBeInstanceOf(Shipment::class)
        ->rates->toHaveCount(1);
});

it('can find a shipment', function () {
    mockApi([
        ShipmentMock::make()
            ->forId('shp_123'),
    ]);

    $shipment = $this->api->find('shp_123');

    expect($shipment)
        ->toBeInstanceOf(Shipment::class)
        ->id->toBe('shp_123');
});

it('throws an exception for not found shipments', function () {
    mockApi([
        ShipmentMock::make()
            ->forId('shp_123')
            ->notFound(),
    ]);

    $this->api->find('shp_123');
})->throws(ShipmentRetrievalFailed::class, 'The requested resource could not be found.');

it('throws a shipment creation exception with the carrier message when no rates are returned', function () {
    mockApi([
        ShipmentMock::make()
            ->forCreation()
            ->withMessage('The carrier account is not configured for this shipment.'),
    ]);

    $this->api->create([]);
})->throws(
    ShipmentCreationFailed::class,
    'We were unable to rate your shipment at this time - message from EasyPost: The carrier account is not configured for this shipment.',
);
