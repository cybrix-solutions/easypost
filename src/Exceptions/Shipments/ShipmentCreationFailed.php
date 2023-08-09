<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Shipments;

use Exception;

final class ShipmentCreationFailed extends Exception
{
    public static function make(string $message): self
    {
        return new self(__('easypost::exceptions.shipment_api_create_fail', ['message' => $message]));
    }

    public static function forPurchase(string $message): self
    {
        return new self(__('easypost::exceptions.shipment_purchased_fail', ['message' => $message]));
    }
}
