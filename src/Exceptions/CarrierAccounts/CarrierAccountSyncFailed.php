<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\CarrierAccounts;

use Exception;

final class CarrierAccountSyncFailed extends Exception
{
    public static function because(string $reason): self
    {
        return new self(__('easypost::exceptions.carrier_sync_api_fail', ['message' => $reason]));
    }
}
