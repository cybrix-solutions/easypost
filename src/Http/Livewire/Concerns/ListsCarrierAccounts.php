<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Http\Livewire\Concerns;

use CybrixSolutions\EasyPost\Contracts\CarrierAccount;

/**
 * @mixin \Livewire\Component
 */
trait ListsCarrierAccounts
{
    public function getRowsProperty()
    {
        return $this->rowsQuery->get([
            'id',
            'easypost_id',
            'name',
            'type',
            'billing_type',
            'default',
            'deactivated_at',
        ]);
    }

    public function getRowsQueryProperty()
    {
        return app(CarrierAccount::class)::query()
            ->orderBy('type')
            ->orderBy('name');
    }

    public function hydrateListsCarrierAccounts(): void
    {
        $this->listeners['carrier_account.added'] = '$refresh';
        $this->listeners['carrier_account.synced'] = 'onSync';
        $this->listeners['carrier_account.updated'] = '$refresh';
    }

    public function onSync(): void
    {

    }
}
