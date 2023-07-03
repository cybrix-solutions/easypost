<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\CarrierAccounts;

use Exception;

final class CarrierAccountUpdateFailed extends Exception
{
    public static function because(string $reason): self
    {
        return new self(__('easypost::exceptions.carrier_account_update_api_fail', ['message' => $reason]));
    }
}
