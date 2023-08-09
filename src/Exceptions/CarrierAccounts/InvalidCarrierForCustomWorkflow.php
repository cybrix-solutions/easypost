<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\CarrierAccounts;

use InvalidArgumentException;

final class InvalidCarrierForCustomWorkflow extends InvalidArgumentException
{
    public static function forCarrier(string $carrier): self
    {
        return new self("No workflow found for carrier [{$carrier}]");
    }

    public static function unsupported(string $carrier): self
    {
        return new self("Unsupported carrier type for custom workflow: {$carrier}");
    }
}
