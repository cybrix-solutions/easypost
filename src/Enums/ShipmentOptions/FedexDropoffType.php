<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Enums\ShipmentOptions;

enum FedexDropoffType: string
{
    case RegularPickup = 'REGULAR_PICKUP';
    case ScheduledPickup = 'SCHEDULED_PICKUP';
    case RetailLocation = 'RETAIL_LOCATION';
    case Station = 'STATION';
    case DropBox = 'DROP_BOX';
}
