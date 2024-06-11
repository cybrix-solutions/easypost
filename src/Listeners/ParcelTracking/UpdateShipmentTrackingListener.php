<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Listeners\ParcelTracking;

use CybrixSolutions\EasyPost\Events\ParcelTracking\ParcelTrackingWasUpdated;
use CybrixSolutions\EasyPost\Jobs\ParcelTracking\UpdateShipmentTrackingJob;

class UpdateShipmentTrackingListener
{
    public function handle(ParcelTrackingWasUpdated $event): void
    {
        // Dispatching the job this way allows you to re-bind the job to a different implementation
        // in a service provider if needed.
        $job = app(UpdateShipmentTrackingJob::class, ['parcelId' => $event->parcel->id]);

        dispatch($job);
    }
}
