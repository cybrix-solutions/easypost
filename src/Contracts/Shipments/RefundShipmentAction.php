<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Shipments;

use CybrixSolutions\EasyPost\Contracts\Models\Shipment;

interface RefundShipmentAction
{
    public function __invoke(Shipment $shipment): Shipment;
}
