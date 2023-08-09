<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Addresses;

use EasyPost\Exception\Api\InvalidRequestException;
use Exception;

final class AddressValidationFailed extends Exception
{
    public array $errors = [];

    public static function because(string $reason): self
    {
        return new self(__('easypost::exceptions.address_validate_fail', ['message' => $reason]));
    }

    public static function fromInvalidRequest(InvalidRequestException $e): self
    {
        $instance = new self($e->getMessage());

        $instance->errors = $e->errors;

        return $instance;
    }
}
