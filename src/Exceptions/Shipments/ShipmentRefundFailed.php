<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Shipments;

use Exception;

final class ShipmentRefundFailed extends Exception
{
    public static function because(string $message): self
    {
        return new self(__('easypost::exceptions.shipment_api_refund_fail', ['message' => $message]));
    }
}
