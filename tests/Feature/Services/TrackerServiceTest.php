<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Exceptions\ParcelTracking\ParcelTrackingFailed;
use CybrixSolutions\EasyPost\Services\TrackerService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\ParcelTracking\TrackerMock;
use EasyPost\Tracker;
use EasyPost\TrackingDetail;

beforeEach(function () {
    $this->api = app(TrackerService::class);
});

it('can retrieve a tracker by its id', function () {
    mockApi([
        TrackerMock::make()
            ->forId('trk_123'),
    ]);

    $tracker = $this->api->retrieve('trk_123');

    expect($tracker)
        ->toBeInstanceOf(Tracker::class)
        ->id->toBe('trk_123')
        ->tracking_details->toBeArray()
        ->tracking_details->toContainOnlyInstancesOf(TrackingDetail::class)
        ->tracking_details->toHaveCount(2);
});

it('throws an exception when a tracker is not found', function () {
    mockApi([
        TrackerMock::make()->notFound(),
    ]);

    $this->api->retrieve('trk_123');
})->throws(ParcelTrackingFailed::class, 'The requested resource could not be found.');

it('can create a tracker', function () {
    mockApi([
        TrackerMock::make()
            ->usingMethod('post')
            ->forId('trk_123')
            ->forTrackingCode('123456')
            ->forCarrier(CarrierEnum::BetterTrucks),
    ]);

    $tracker = $this->api->create('123456', CarrierEnum::BetterTrucks);

    expect($tracker)
        ->id->toBe('trk_123')
        ->tracking_code->toBe('123456')
        ->carrier->toBe(CarrierEnum::BetterTrucks->nameForTracker());
});
