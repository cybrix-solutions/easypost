<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\Shipments;

use CybrixSolutions\EasyPost\Contracts\Models\Shipment;
use CybrixSolutions\EasyPost\Contracts\Shipments\RefundShipmentAction as RefundShipmentActionContract;
use CybrixSolutions\EasyPost\Enums\ShipmentRefundStatusEnum;
use CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum;
use CybrixSolutions\EasyPost\Events\Shipments\ShipmentWasRefunded;
use CybrixSolutions\EasyPost\Facades\EasyPost;
use CybrixSolutions\EasyPost\Services\ShipmentService;

class RefundShipmentAction implements RefundShipmentActionContract
{
    public function __construct(protected ShipmentService $api)
    {
    }

    public function __invoke(Shipment $shipment): Shipment
    {
        if ($shipment->isVoided()) {
            return $shipment;
        }

        $refundedShipment = $this->api->refund($shipment->easypost_id);

        $shipment->refund_status = ShipmentRefundStatusEnum::tryFrom($refundedShipment->refund_status)?->value;
        $shipment->status = ShipmentStatusEnum::Cancelled;

        if ($shipment->refund_status !== ShipmentRefundStatusEnum::Rejected) {
            $shipment->fill([
                'voided_at' => now(),
                'voided_by' => EasyPost::authenticatedUserId(),
            ]);
        }

        $shipment->save();

        if ($shipment->refund_status !== ShipmentRefundStatusEnum::Rejected) {
            ShipmentWasRefunded::dispatch($shipment);
        }

        return $shipment;
    }
}
