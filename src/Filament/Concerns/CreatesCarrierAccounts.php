<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Filament\Concerns;

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Filament\Actions\CreateCarrierAccountAction;
use CybrixSolutions\EasyPost\Services\CarrierService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component as SchemaComponent;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use ValueError;

/**
 * @property-read null|CarrierService $selectedCarrierService
 *
 * @mixin GeneratesCarrierAccountFormSchema
 * @mixin Component
 */
trait CreatesCarrierAccounts
{
    public ?string $carrierSearch = '';

    public ?array $createCarrierData = [];

    #[Locked]
    public ?string $selectedCarrierType = null;

    #[Computed]
    public function selectedCarrierService(): ?CarrierService
    {
        if (! $this->selectedCarrierType) {
            return null;
        }

        return CarrierService::fromType($this->selectedCarrierType);
    }

    public function createCarrierForm(Schema $form): Schema
    {
        return $form
            ->statePath('createCarrierData')
            ->schema(fn () => [
                $this->getCarrierNameField(),
                ...Arr::wrap($this->carrierFormSchema($this->selectedCarrierService)),
            ]);
    }

    public function carrierSearchForm(Schema $form): Schema
    {
        return $form
            ->schema([
                $this->getCarrierSearchField(),
            ]);
    }

    public function createCarrierAccountAction(): Action|CreateCarrierAccountAction
    {
        return CreateCarrierAccountAction::make()
            ->authorize('create', config('easypost.models.carrier_account'))
            ->tooltip(fn (): ?string => $this->hasProductionApiKey ? null : __('easypost::livewire/carriers.accounts.production_api_key_required'));
    }

    public function selectCarrierType(string $type): void
    {
        try {
            $enum = CarrierEnum::from($type);
        } catch (ValueError) {
            Notification::make()
                ->danger()
                ->title(__('easypost::validation.invalid_carrier_chosen'))
                ->send();

            return;
        }

        if ($enum->isDisabled()) {
            Notification::make()
                ->danger()
                ->title(__('easypost::validation.invalid_carrier_chosen'))
                ->send();

            return;
        }

        $this->resetValidation();

        $this->createCarrierData = [];

        $this->selectedCarrierType = $enum->value;
    }

    protected function getCarrierSearchField(): SchemaComponent|TextInput
    {
        return TextInput::make('carrierSearch')
            ->hiddenLabel()
            ->placeholder(__('easypost::livewire/carriers.accounts.actions.create.search.placeholder'))
            ->debounce()
            ->prefixIcon(FilamentIcon::resolve('easypost::search') ?? 'heroicon-m-magnifying-glass')
            ->inlinePrefix()
            ->type('search');
    }

    protected function getCarrierNameField(): SchemaComponent|TextInput
    {
        return TextInput::make('name')
            ->label(__('easypost::labels.carrier_account_form.account_name'))
            ->validationAttribute(strtolower(__('easypost::labels.carrier_account_form.account_name')))
            ->placeholder(__('easypost::labels.carrier_account_form.account_name_placeholder'))
            ->required()
            ->maxLength(255)
            ->minLength(3);
    }
}
