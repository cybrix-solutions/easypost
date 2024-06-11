<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\CarrierAccounts;

use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Services\CarrierService;

interface UpdateCarrierAction
{
    public function __invoke(CarrierAccount $account, array $input): CarrierAccount;

    public function withCarrierService(CarrierService $carrierService): self;

    public function withStoredValues(array $values): self;

    public function withoutValidation(): self;
}
