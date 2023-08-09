<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Http\Livewire\Concerns;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\ActivateCarrierAccountAction;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\DeactivateCarrierAccountAction;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\MakeCarrierDefaultAction;
use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;

/**
 * @mixin \Livewire\Component
 */
trait ModifiesInternalCarrierAccountData
{
    public function activate(string $easypostId, ActivateCarrierAccountAction $activator): void
    {
        $account = app(CarrierAccount::class)::findByEasyPostId($easypostId);

        $this->authorize('modify', $account);

        $activator($account);

        $this->onAccountModify('activate', $account);
    }

    public function deactivate(string $easypostId, DeactivateCarrierAccountAction $deactivator): void
    {
        $account = app(CarrierAccount::class)::findByEasyPostId($easypostId);

        $this->authorize('modify', $account);

        $deactivator($account);

        $this->onAccountModify('deactivate', $account);
    }

    public function makeDefault(string $easypostId, MakeCarrierDefaultAction $action): void
    {
        $account = app(CarrierAccount::class)::findByEasyPostId($easypostId);

        $this->authorize('makeDefault', $account);

        $action($account);

        $this->onAccountModify('makeDefault', $account);
    }

    /**
     * This is meant to be overridden in your Livewire component when you need to perform any extra steps
     * for your UI.
     */
    protected function onAccountModify(string $action, CarrierAccount $account): void
    {
        //
    }
}
