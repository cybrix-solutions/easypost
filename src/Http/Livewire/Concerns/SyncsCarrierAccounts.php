<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Http\Livewire\Concerns;

use CybrixSolutions\EasyPost\Contracts\CarrierAccount;
use CybrixSolutions\EasyPost\Contracts\SyncCarriersAction;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountSyncFailed;
use function CybrixSolutions\EasyPost\hasApiKey;

/**
 * @property-read bool $hasApiKey
 *
 * @mixin \Livewire\Component
 */
trait SyncsCarrierAccounts
{
    public bool $show = false;

    public ?string $errorMessage = null;

    public function getHasApiKeyProperty(): bool
    {
        return hasApiKey();
    }

    public function confirm(): void
    {
        if (! $this->hasApiKey) {
            return;
        }

        $this->reset('errorMessage');
        $this->show = true;
    }

    public function sync(SyncCarriersAction $syncer): void
    {
        if (! $this->hasApiKey) {
            return;
        }

        $this->authorize('sync', [app(CarrierAccount::class)::class, ...$this->authorizeSyncWith()]);

        $this->reset('errorMessage');

        $syncer->withContext($this->syncContext());
        $filter = $this->syncFilter();
        if (is_callable($filter)) {
            $syncer->withAccountFilter($filter);
        }

        try {
            $syncer();
        } catch (CarrierAccountSyncFailed $e) {
            $this->errorMessage = $e->getMessage();

            return;
        }

        $this->reset('show');

        $this->onSynced();

        $this->emit('carrier_account.synced');
    }

    /*
     * To be overridden in the consuming application, if necessary.
     */

    protected function authorizeSyncWith(): array
    {
        return [];
    }

    protected function syncContext(): array
    {
        return [];
    }

    protected function syncFilter(): ?callable
    {
        return null;
    }

    protected function onSynced(): void
    {
    }
}
