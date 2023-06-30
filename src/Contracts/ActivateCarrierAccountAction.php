<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts;

interface ActivateCarrierAccountAction
{
    public function __invoke(CarrierAccount $account): void;
}
