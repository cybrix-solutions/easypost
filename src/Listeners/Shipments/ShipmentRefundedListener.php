<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Listeners\Shipments;

use CybrixSolutions\EasyPost\Contracts\Models\Parcel;
use CybrixSolutions\EasyPost\Events\Shipments\ShipmentWasRefunded;

final class ShipmentRefundedListener
{
    public function handle(ShipmentWasRefunded $event): void
    {
        $event->shipment
            ->parcels()
            ->whereNull('voided_at')
            ->cursor()
            ->each(function (Parcel $parcel) use ($event) {
                $parcel->fill([
                    'voided_at' => $event->shipment->voided_at,
                    'voided_by' => $event->shipment->voided_by,
                ]);

                $parcel->saveQuietly();
            });
    }
}
