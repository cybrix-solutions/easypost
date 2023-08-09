<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\CarrierAccounts;

use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;

interface MakeCarrierDefaultAction
{
    public function __invoke(CarrierAccount $account): void;
}
