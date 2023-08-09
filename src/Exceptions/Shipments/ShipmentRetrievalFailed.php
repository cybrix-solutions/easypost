<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Shipments;

use Exception;

final class ShipmentRetrievalFailed extends Exception
{
    public static function notFound(string $message): self
    {
        return new self($message);
    }

    public static function generalError(string $message): self
    {
        return new self(__('easypost::exceptions.shipment_api_find_fail', ['message' => $message]));
    }
}
