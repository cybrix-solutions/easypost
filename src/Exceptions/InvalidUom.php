<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions;

use Exception;

final class InvalidUom extends Exception
{
    public static function length(string $uom): self
    {
        return new self("Invalid length unit of measurement: {$uom}");
    }

    public static function weight(string $uom): self
    {
        return new self("Invalid weight unit of measurement: {$uom}");
    }
}
