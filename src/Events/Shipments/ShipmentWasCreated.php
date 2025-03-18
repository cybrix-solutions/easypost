<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Events\Shipments;

use CybrixSolutions\EasyPost\Contracts\Models\Shipment;
use EasyPost\Shipment as EasyPostShipment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ShipmentWasCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Shipment $shipment, public ?EasyPostShipment $purchasedShipment = null) {}
}
