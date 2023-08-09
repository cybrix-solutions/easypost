<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Models;

interface Shipment
{
    public function isDelivered(): bool;

    public function isPickedUp(): bool;

    public function isVoided(): bool;

    public static function findByEasyPostId(string $id): self;

    public function canBeVoided(): bool;

    public function refreshTracking(): void;
}
