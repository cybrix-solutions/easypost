<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\Shipments;

use CybrixSolutions\EasyPost\Contracts\Models\Shipment;
use CybrixSolutions\EasyPost\Contracts\Shipments\DeleteShipmentAction as DeleteShipmentActionContract;
use CybrixSolutions\EasyPost\Contracts\Shipments\RefundShipmentAction as RefundShipmentActionContract;
use CybrixSolutions\EasyPost\Events\Shipments\ShipmentWasDeleted;

class DeleteShipmentAction implements DeleteShipmentActionContract
{
    public function __invoke(Shipment $shipment): void
    {
        if ($shipment->canBeVoided()) {
            // There's a chance of this failing, so we'll wrap this in `rescue` to prevent this from
            // terminating the request.
            rescue(fn () => app(RefundShipmentActionContract::class)($shipment));
        }

        $shipment->delete();

        ShipmentWasDeleted::dispatch($shipment);
    }
}
