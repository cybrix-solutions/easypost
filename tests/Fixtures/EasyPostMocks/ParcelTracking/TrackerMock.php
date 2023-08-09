<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\ParcelTracking;

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\EasyPostMock;

final class TrackerMock extends EasyPostMock
{
    protected string $urlPattern = '/v2\\/trackers\\/\\S*$/';

    protected ?string $desiredId = null;

    protected ?string $trackingCode = null;

    protected ?ShipmentStatusEnum $status = null;

    protected ?string $shipmentId = null;

    protected ?CarrierEnum $carrier = null;

    public function urlPattern(): string
    {
        return $this->method === 'post'
            ? '/v2\\/trackers$/'
            : $this->urlPattern;
    }

    public function forId(string $id): self
    {
        // If the method is 'post', we're creating a new tracker.
        if ($this->method !== 'post') {
            $this->urlPattern = '/v2\\/trackers\\/' . $id . '$/';
        }

        $this->desiredId = $id;

        return $this;
    }

    public function forTrackingCode(string $trackingCode): self
    {
        $this->trackingCode = $trackingCode;

        return $this;
    }

    public function forStatus(ShipmentStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function forShipmentId(string $shipmentId): self
    {
        $this->shipmentId = $shipmentId;

        return $this;
    }

    public function forCarrier(CarrierEnum $carrier): self
    {
        $this->carrier = $carrier;

        return $this;
    }

    protected function getPayload(): array
    {
        return [
            'id' => $this->desiredId ?? 'trk_f7ed99b497a944fb84f99bf6a9a5f8ea',
            'object' => 'Tracker',
            'mode' => 'test',
            'tracking_code' => $this->trackingCode ?? '9461200106068143633117',
            'status' => $this->status?->value ?? 'pre_transit',
            'status_detail' => 'status_update',
            'created_at' => '2022-10-17T17:18:08Z',
            'updated_at' => '2022-10-17T17:18:08Z',
            'signed_by' => null,
            'weight' => null,
            'est_delivery_date' => '2022-10-17T17:18:08Z',
            'shipment_id' => $this->shipmentId ?? 'shp_b3b2b84b0ad14a2ca926ab8fddd02ef2',
            'carrier' => $this->carrier?->nameForTracker() ?? 'USPS',
            'tracking_details' => [
                [
                    'object' => 'TrackingDetail',
                    'message' => 'Pre-Shipment Info Sent to USPS',
                    'description' => null,
                    'status' => 'pre_transit',
                    'status_detail' => 'status_update',
                    'datetime' => '2022-09-17T17:18:08Z',
                    'source' => 'USPS',
                    'carrier_code' => null,
                    'tracking_location' => [
                        'object' => 'TrackingLocation',
                        'city' => null,
                        'state' => null,
                        'country' => null,
                        'zip' => null,
                    ],
                ],
                [
                    'object' => 'TrackingDetail',
                    'message' => 'Shipping Label Created',
                    'description' => null,
                    'status' => 'pre_transit',
                    'status_detail' => 'status_update',
                    'datetime' => '2022-09-18T05:55:08Z',
                    'source' => 'USPS',
                    'carrier_code' => null,
                    'tracking_location' => [
                        'object' => 'TrackingLocation',
                        'city' => 'HOUSTON',
                        'state' => 'TX',
                        'country' => null,
                        'zip' => '77063',
                    ],
                ],
            ],
        ];
    }
}
