<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services;

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Exceptions\ParcelTracking\ParcelTrackingFailed;
use CybrixSolutions\EasyPost\Services\Api\EasyPostClient;
use EasyPost\Exception\Api\ApiException;
use EasyPost\Tracker;

final readonly class TrackerService
{
    public function __construct(private EasyPostClient $api) {}

    public function retrieve(string $trackerId): Tracker
    {
        try {
            return $this->api->tracker->retrieve($trackerId);
        } catch (ApiException $e) {
            throw ParcelTrackingFailed::withMessage($e->getMessage());
        }
    }

    public function create(string $trackingCode, string|CarrierEnum|null $carrier = null): Tracker
    {
        if (is_string($carrier)) {
            $carrier = CarrierEnum::tryFrom($carrier);
        }

        try {
            return $this->api->tracker->create(array_filter([
                'tracking_code' => $trackingCode,
                'carrier' => $carrier?->nameForTracker(),
            ]));
        } catch (ApiException $e) {
            throw ParcelTrackingFailed::cannotCreate($e->getMessage());
        }
    }
}
