<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Http\Livewire\Concerns;

use CybrixSolutions\EasyPost\Contracts\CarrierAccount;
use CybrixSolutions\EasyPost\Contracts\UpdateCarrierAction;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountUpdateFailed;
use CybrixSolutions\EasyPost\Services\CarrierService;

/**
 * @property-read null|\CybrixSolutions\EasyPost\Enums\CarrierEnum $carrierEnum
 * @property-read null|\CybrixSolutions\EasyPost\Services\CarrierService $carrierService
 * @property-read string $formTitle
 *
 * @mixin \Livewire\Component
 */
trait EditsCarrierAccount
{
    public bool $show = false;

    public string $editingId = '';

    public string $carrierType = '';

    public ?string $errorMessage = '';

    public array $state = [];

    public function getCarrierEnumProperty(): ?CarrierEnum
    {
        return $this->carrierType
            ? CarrierEnum::from($this->carrierType)
            : null;
    }

    public function getCarrierServiceProperty(): ?CarrierService
    {
        return $this->editingId
            ? CarrierService::fromAccount($this->editingId)
            : null;
    }

    public function getFormTitleProperty(): string
    {
        return $this->carrierEnum
            ? __('easypost::labels.carrier_account_form.edit_title', ['type' => $this->carrierEnum->label()])
            : '';
    }

    public function edit(string $easypostId): void
    {
        $account = app(CarrierAccount::class)::findByEasyPostId($easypostId);

        $this->authorize('edit', $account);

        $this->reset('errorMessage', 'state');
        $this->resetErrorBag();

        $this->editingId = $account->easypost_id;
        $this->carrierType = $account->type->value;

        $this->state = [
            'name' => $account->name,
            ...$this->carrierService->storedValues(),
        ];

        $this->show = true;
    }

    public function update(UpdateCarrierAction $updater): void
    {
        if (! $this->editingId) {
            return;
        }

        $account = app(CarrierAccount::class)::findByEasyPostId($this->editingId);

        $this->authorize('edit', $account);
        $this->resetErrorBag();
        $this->reset('errorMessage');

        try {
            $updater
                ->withCarrierService($this->carrierService)
                ->withStoredValues($this->carrierService->storedValues());

            $updater($account, $this->state);
        } catch (CarrierAccountUpdateFailed $e) {
            $this->errorMessage = $e->getMessage();

            return;
        }

        $this->reset('show', 'editingId', 'carrierType', 'state');

        $this->onAccountUpdated($account);

        $this->emit('carrier_account.updated', $account->easypost_id);
    }

    public function hydrateEditsCarrierAccount(): void
    {
        $this->listeners['carrier_account.edit'] = 'edit';
    }

    /**
     * This is meant to be overridden by the consuming class to perform any kind of UI updates
     * that are needed.
     */
    protected function onAccountUpdated(CarrierAccount $account): void
    {
        //
    }
}
