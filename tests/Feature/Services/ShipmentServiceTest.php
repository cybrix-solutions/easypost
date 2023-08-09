<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Exceptions\Shipments\ShipmentRetrievalFailed;
use CybrixSolutions\EasyPost\Services\ShipmentService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Shipments\ShipmentMock;
use EasyPost\Shipment;

beforeEach(function () {
    $this->api = app(ShipmentService::class);
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
