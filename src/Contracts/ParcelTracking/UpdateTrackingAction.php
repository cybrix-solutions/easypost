<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\ParcelTracking;

use CybrixSolutions\EasyPost\Contracts\Models\Parcel;

interface UpdateTrackingAction
{
    /**
     * @throws \CybrixSolutions\EasyPost\Exceptions\ParcelTracking\ParcelTrackingFailed
     */
    public function __invoke(Parcel $parcel): void;
}
