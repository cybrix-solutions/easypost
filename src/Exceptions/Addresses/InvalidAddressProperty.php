<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Addresses;

use Exception;

final class InvalidAddressProperty extends Exception
{
    public static function make(string $property): self
    {
        return new self("Invalid pending address property: {$property}");
    }
}
