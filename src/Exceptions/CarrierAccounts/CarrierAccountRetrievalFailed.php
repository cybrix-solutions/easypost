<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\CarrierAccounts;

use Exception;

final class CarrierAccountRetrievalFailed extends Exception
{
    public static function notFound(string $message): self
    {
        return new self($message);
    }

    public static function generalError(string $message): self
    {
        return new self(__('easypost::exceptions.carrier_account_retrieve_fail', ['message' => $message]));
    }
}
