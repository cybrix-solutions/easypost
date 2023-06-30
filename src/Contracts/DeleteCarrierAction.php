<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts;

interface DeleteCarrierAction
{
    public function __invoke(CarrierAccount $account): void;
}
