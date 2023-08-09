<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Http\Livewire\Concerns;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\AddCarrierAccountAction;
use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountCreationFailed;
use function CybrixSolutions\EasyPost\hasApiKey;
use CybrixSolutions\EasyPost\Services\CarrierService;
use Illuminate\Support\Collection;
use ValueError;

/**
 * @property-read null|\CybrixSolutions\EasyPost\Enums\CarrierEnum $carrierEnum
 * @property-read null|\CybrixSolutions\EasyPost\Services\CarrierService $carrierService
 * @property-read Collection<int, \CybrixSolutions\EasyPost\Enums\CarrierEnum> $filteredCarrierTypes
 * @property-read string $formTitle
 * @property-read bool $hasApiKey
 *
 * @mixin \Livewire\Component
 */
trait AddsCarrierAccounts
{
    public bool $show = false;

    public string $carrierType = '';

    public string $carrierSearch = '';

    public array $state = [
        'name' => '',
        'accepted_terms' => false,
        'credentials' => [],
        'test_credentials' => [],
        'registration_data' => [], // For custom workflows
    ];

    public ?string $errorMessage = null;

    public function getCarrierEnumProperty(): ?CarrierEnum
    {
        return $this->carrierType ? CarrierEnum::from($this->carrierType) : null;
    }

    public function getCarrierServiceProperty(): ?CarrierService
    {
        return $this->carrierType
            ? CarrierService::fromType($this->carrierType)
            : null;
    }

    public function getFilteredCarrierTypesProperty(): Collection
    {
        return CarrierEnum::fromSearch($this->carrierSearch);
    }

    public function getFormTitleProperty(): string
    {
        return $this->carrierType
            ? __('easypost::labels.carrier_account_form.carrier_add_title', ['name' => $this->carrierEnum->label()])
            : __('easypost::labels.carrier_account_form.general_add_title');
    }

    public function getHasApiKeyProperty(): bool
    {
        return hasApiKey();
    }

    public function back(): void
    {
        $this->reset('carrierType');
    }

    public function add(): void
    {
        if (! $this->hasApiKey) {
            return;
        }

        $this->reset('carrierSearch', 'carrierType', 'state', 'errorMessage');
        $this->show = true;
    }

    public function selectCarrier(string $carrier): void
    {
        try {
            $enum = CarrierEnum::from($carrier);
        } catch (ValueError) {
            $this->errorMessage = __('easypost::validation.invalid_carrier_chosen');

            return;
        }

        $this->reset('state', 'errorMessage');
        $this->carrierType = $enum->value;
    }

    public function store(AddCarrierAccountAction $action): void
    {
        if (! $this->carrierService || ! $this->hasApiKey) {
            return;
        }

        $this->authorize('create', [app(CarrierAccount::class)::class, ...$this->authorizeAddWith()]);

        $this->resetErrorBag();
        $this->reset('errorMessage');

        $action
            ->withCarrierService($this->carrierService)
            ->withContext($this->addContext())
            ->withReference($this->addReference());

        try {
            $account = $action($this->state);
        } catch (CarrierAccountCreationFailed $e) {
            $this->errorMessage = $e->getMessage();

            return;
        }

        $this->reset('state', 'show', 'errorMessage', 'carrierType', 'carrierSearch');
        $this->emit('carrier_account.added', $account->easypost_id);
        $this->onAdded();
    }

    public function hydrateAddsCarrierAccounts(): void
    {
        $this->listeners['add-carrier'] = 'add';
    }

    /*
     * To be overridden in the consuming application, if necessary.
     */

    protected function authorizeAddWith(): array
    {
        return [];
    }

    protected function addContext(): array
    {
        return [];
    }

    protected function addReference(): ?string
    {
        return null;
    }

    protected function onAdded(): void
    {
    }
}
