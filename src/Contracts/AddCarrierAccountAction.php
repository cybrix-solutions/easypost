<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts;

use CybrixSolutions\EasyPost\Services\CarrierService;

interface AddCarrierAccountAction
{
    public function withCarrierService(CarrierService $service): self;

    /**
     * @throws \CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountCreationFailed
     */
    public function __invoke(array $input): CarrierAccount;
}
