<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\CarrierAccounts;

use Closure;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountSyncFailed;

interface SyncCarriersAction
{
    /**
     * @throws CarrierAccountSyncFailed
     */
    public function __invoke(): void;

    public function withContext(array $context): static;

    public function filterAccountsWith(?Closure $callback): static;

    public function withAccountFilter(?Closure $callback): static;
}
