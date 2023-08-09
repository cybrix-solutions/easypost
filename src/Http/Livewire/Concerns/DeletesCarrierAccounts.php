<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Http\Livewire\Concerns;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\DeleteCarrierAction;
use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountDeletionFailed;

/**
 * @mixin \Livewire\Component
 */
trait DeletesCarrierAccounts
{
    public bool $showDelete = false;

    public ?CarrierAccount $deleting = null;

    public ?string $deleteError = null;

    public function confirmDelete(string $easypostId): void
    {
        $this->deleting = app(CarrierAccount::class)::findByEasyPostId($easypostId);
        $this->reset('deleteError');
        $this->showDelete = true;
        $this->emit('carrier_account.confirming-delete', $easypostId);
    }

    public function deleteCarrier(DeleteCarrierAction $deleter): void
    {
        if (! $this->deleting) {
            return;
        }

        $this->authorize('delete', $this->deleting);

        try {
            $deleter($this->deleting);
        } catch (CarrierAccountDeletionFailed $e) {
            $this->deleteError = $e->getMessage();

            return;
        }

        $this->emit('carrier_account.deleted', $this->deleting->easypost_id);
        $this->reset('deleting', 'showDelete', 'deleteError');
    }

    public function hydrateDeletesCarrierAccounts(): void
    {
        $this->listeners['carrier_account.confirm-delete'] = 'confirmDelete';
    }
}
