<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Jobs\ParcelTracking;

use CybrixSolutions\EasyPost\Contracts\Models\Parcel;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateShipmentTrackingJob
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public Parcel $parcel;

    public function __construct(Parcel $parcel)
    {
        $this->parcel = $parcel->withoutRelations();
    }

    public function handle(): void
    {
        /** @var \CybrixSolutions\EasyPost\Models\Shipment $shipment */
        if (! $shipment = $this->parcel->shipment) {
            return;
        }

        // If the shipment hasn't been marked as picked up yet, and the package was, mark the
        // shipment as picked up.
        if ($this->parcel->isPickedUp() && ! $shipment->isPickedUp()) {
            $shipment->picked_up_at = $this->parcel->picked_up_at;
        }

        // If the shipment hasn't been marked as delivered yet, and this is package has,
        // mark the shipment as delivered too.
        if (! $shipment->isDelivered() && $this->parcel->isDelivered()) {
            $shipment->delivered_at = $this->parcel->delivered_at;
            $shipment->signed_by = $this->parcel->signed_by;
        }

        $shipment->status = $this->parcel->status;

        if ($shipment->isDirty()) {
            $shipment->saveQuietly();
        }
    }
}
