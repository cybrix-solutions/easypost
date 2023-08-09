<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Shipments;

use CybrixSolutions\EasyPost\Contracts\Models\Shipment;

interface DeleteShipmentAction
{
    public function __invoke(Shipment $shipment);
}
