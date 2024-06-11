<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost;

use CybrixSolutions\EasyPost\Events\ParcelTracking\ParcelTrackingWasUpdated;
use CybrixSolutions\EasyPost\Events\Shipments\ShipmentWasRefunded;
use CybrixSolutions\EasyPost\Listeners\ParcelTracking\UpdateShipmentTrackingListener;
use CybrixSolutions\EasyPost\Listeners\Shipments\ShipmentRefundedListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(
            events: ParcelTrackingWasUpdated::class,
            listener: UpdateShipmentTrackingListener::class,
        );

        Event::listen(
            events: ShipmentWasRefunded::class,
            listener: ShipmentRefundedListener::class,
        );
    }
}
