<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Http\Livewire\Concerns;

use CybrixSolutions\EasyPost\Contracts\ActivateCarrierAccountAction;
use CybrixSolutions\EasyPost\Contracts\CarrierAccount;
use CybrixSolutions\EasyPost\Contracts\DeactivateCarrierAccountAction;
use CybrixSolutions\EasyPost\Contracts\MakeCarrierDefaultAction;

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
