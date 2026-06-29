<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\CarrierAccounts;

use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountCreationFailed;
use CybrixSolutions\EasyPost\Services\CarrierService;

interface AddCarrierAccountAction
{
    /**
     * @throws CarrierAccountCreationFailed
     */
    public function __invoke(array $input): CarrierAccount;

    public function withCarrierService(CarrierService $service): self;

    public function withContext(array $context): self;

    public function withReference(?string $reference): self;

    public function withoutValidation(): self;
}
