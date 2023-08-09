<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Shipments;

use Exception;

final class InvalidDeliveryConfirmation extends Exception
{
    public static function fromEasyPostValue(?string $value): self
    {
        return new self("The delivery confirmation value `{$value}` is either not valid or not supported at this time.");
    }
}
