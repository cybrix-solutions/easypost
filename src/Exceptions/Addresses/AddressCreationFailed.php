<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Addresses;

use Exception;

final class AddressCreationFailed extends Exception
{
    public static function because(string $reason): self
    {
        return new self(__('easypost::exceptions.address_create_api_fail', ['message' => $reason]));
    }
}
