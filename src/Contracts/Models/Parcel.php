<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Models;

interface Parcel
{
    public function getDimWeightDivisor(): int|float;

    public function adjustParcelDimensions(): void;

    public function isPickedUp(): bool;

    public function isDelivered(): bool;

    public function isVoided(): bool;

    /**
     * The width generated labels should be, in inches.
     */
    public function labelWidth(): int;

    /**
     * The height generated labels should be, in inches.
     */
    public function labelHeight(): int;

    public function refreshTracking(Shipment $shipment = null): void;
}
