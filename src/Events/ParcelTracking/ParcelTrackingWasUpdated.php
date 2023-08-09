<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Events\ParcelTracking;

use CybrixSolutions\EasyPost\Contracts\Models\Parcel;
use CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class ParcelTrackingWasUpdated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Parcel $parcel,
        public ?ShipmentStatusEnum $newStatus = null,
        public bool $statusChanged = true,
    ) {
    }
}
