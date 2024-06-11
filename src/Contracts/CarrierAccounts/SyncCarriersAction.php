<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\CarrierAccounts;

use Closure;

interface SyncCarriersAction
{
    /**
     * @throws \CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountSyncFailed
     */
    public function __invoke(): void;

    public function withContext(array $context): static;

    public function filterAccountsWith(?Closure $callback): static;
}
