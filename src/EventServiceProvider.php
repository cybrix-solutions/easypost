<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost;

use CybrixSolutions\EasyPost\Events\ParcelTracking\ParcelTrackingWasUpdated;
use CybrixSolutions\EasyPost\Events\Shipments\ShipmentWasRefunded;
use CybrixSolutions\EasyPost\Listeners\ParcelTracking\UpdateShipmentTrackingListener;
use CybrixSolutions\EasyPost\Listeners\Shipments\ShipmentRefundedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

final class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ParcelTrackingWasUpdated::class => [
            UpdateShipmentTrackingListener::class,
        ],
        ShipmentWasRefunded::class => [
            ShipmentRefundedListener::class,
        ],
    ];
}
