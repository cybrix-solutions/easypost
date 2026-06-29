<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\ParcelTracking;

use CybrixSolutions\EasyPost\Contracts\Models\Parcel;
use CybrixSolutions\EasyPost\Exceptions\ParcelTracking\ParcelTrackingFailed;

interface UpdateTrackingAction
{
    /**
     * @throws ParcelTrackingFailed
     */
    public function __invoke(Parcel $parcel): void;
}
