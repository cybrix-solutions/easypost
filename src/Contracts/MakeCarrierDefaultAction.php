<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts;

interface MakeCarrierDefaultAction
{
    public function __invoke(CarrierAccount $account): void;
}
