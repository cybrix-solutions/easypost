<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services;

use CybrixSolutions\EasyPost\Exceptions\Shipments\ShipmentCreationFailed;
use CybrixSolutions\EasyPost\Exceptions\Shipments\ShipmentRefundFailed;
use CybrixSolutions\EasyPost\Exceptions\Shipments\ShipmentRetrievalFailed;
use CybrixSolutions\EasyPost\Services\Api\EasyPostClient;
use EasyPost\Exception\Api\ApiException;
use EasyPost\Exception\Api\NotFoundException;
use EasyPost\Rate;
use EasyPost\Shipment;

final readonly class ShipmentService
{
    public function __construct(private EasyPostClient $api)
    {
    }

    public function create(array $data): Shipment
    {
        try {
            return $this->api->shipment->create($data);
        } catch (ApiException $e) {
            throw ShipmentCreationFailed::make($e->getMessage());
        }
    }

    public function buy(string|Shipment $shipmentId, Rate $rate, ?float $insurance = null): Shipment
    {
        try {
            $shipment = is_string($shipmentId) ? $this->find($shipmentId) : $shipmentId;

            return $this->api->shipment->buy($shipment->id, array_filter([
                'rate' => $rate,
                'insurance' => $insurance,
            ]));
        } catch (ShipmentRetrievalFailed|ApiException $e) {
            throw ShipmentCreationFailed::forPurchase($e->getMessage());
        }
    }

    public function find(string $shipmentId): Shipment
    {
        try {
            return $this->api->shipment->retrieve($shipmentId);
        } catch (NotFoundException $e) {
            throw ShipmentRetrievalFailed::notFound($e->getMessage());
        } catch (ApiException $e) {
            throw ShipmentRetrievalFailed::generalError($e->getMessage());
        }
    }

    public function refund(string $shipmentId): Shipment
    {
        try {
            $shipment = $this->find($shipmentId);

            return $this->api->shipment->refund($shipment->id);
        } catch (ShipmentRetrievalFailed|ApiException $e) {
            throw ShipmentRefundFailed::because($e->getMessage());
        }
    }
}
