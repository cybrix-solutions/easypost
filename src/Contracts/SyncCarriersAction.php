<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts;

interface SyncCarriersAction
{
    /**
     * @throws \CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountSyncFailed
     */
    public function __invoke(): void;

    public function withContext(array $context): self;
}
