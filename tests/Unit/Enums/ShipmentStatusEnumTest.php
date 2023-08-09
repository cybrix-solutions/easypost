<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum;

it('knows if it is notifiable for a shipment status update', function () {
    config()->set('easypost.notifiable_shipment_statuses', [
        ShipmentStatusEnum::OutForDelivery,
        ShipmentStatusEnum::Delivered,
    ]);

    expect(ShipmentStatusEnum::OutForDelivery->isNotifiable())->toBeTrue()
        ->and(ShipmentStatusEnum::Delivered->isNotifiable())->toBeTrue()
        ->and(ShipmentStatusEnum::AvailableForPickup->isNotifiable())->toBeFalse();
});

it('knows if it is voided status', function () {
    expect(ShipmentStatusEnum::Cancelled->isVoid())->toBeTrue()
        ->and(ShipmentStatusEnum::Delivered->isVoid())->toBeFalse();
});

it('knows if it is a picked up status', function () {
    expect(ShipmentStatusEnum::InTransit->isPickup())->toBeTrue()
        ->and(ShipmentStatusEnum::OutForDelivery->isPickup())->toBeTrue()
        ->and(ShipmentStatusEnum::Delivered->isPickup())->toBeFalse();
});

it('knows if it is a delivered status', function () {
    expect(ShipmentStatusEnum::Delivered->isDelivered())->toBeTrue()
        ->and(ShipmentStatusEnum::OutForDelivery->isDelivered())->toBeFalse();
});
