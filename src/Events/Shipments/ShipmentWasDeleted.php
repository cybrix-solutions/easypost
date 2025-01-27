<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Events\Shipments;

use CybrixSolutions\EasyPost\Contracts\Models\Shipment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ShipmentWasDeleted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public Shipment $shipment) {}
}
