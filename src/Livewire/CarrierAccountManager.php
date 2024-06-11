<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Livewire;

use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Filament\Concerns\CreatesCarrierAccounts;
use CybrixSolutions\EasyPost\Filament\Concerns\EditsCarrierAccounts;
use CybrixSolutions\EasyPost\Filament\Concerns\GeneratesCarrierAccountFormSchema;
use CybrixSolutions\EasyPost\Filament\Concerns\ListsCarrierAccounts;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\FilamentServiceProvider;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use RuntimeException;

use function CybrixSolutions\EasyPost\hasApiKey;

/**
 * @property-read bool $hasProductionApiKey
 */
class CarrierAccountManager extends Component implements HasActions, HasForms, HasTable
{
    use CreatesCarrierAccounts;
    use EditsCarrierAccounts;
    use GeneratesCarrierAccountFormSchema;
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;
    use ListsCarrierAccounts;

    #[Computed]
    public function hasProductionApiKey(): bool
    {
        return hasApiKey();
    }

    public function mount(): void
    {
        throw_unless(
            class_exists(FilamentServiceProvider::class),
            new RuntimeException('Filament is required for this livewire component.'),
        );
    }

    public function render(): View
    {
        return view('easypost::livewire.carrier-account-manager');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(app(CarrierAccount::class)::query())
            ->queryStringIdentifier('carrierAccounts')
            ->columns($this->getCarrierTableColumns())
            ->actions($this->getCarrierTableActions())
            ->headerActions($this->getCarrierTableHeaderActions())
            ->emptyStateHeading(__('easypost::livewire/carriers.accounts.table.empty_state.title'))
            ->emptyStateDescription(
                fn (Table $table): string => $table->hasSearch()
                    ? __('easypost::livewire/carriers.accounts.table.empty_state.description_with_search')
                    : __('easypost::livewire/carriers.accounts.table.empty_state.description_without_search')
            )
            ->emptyStateActions([
                Action::make('create')
                    ->authorize('create')
                    ->button()
                    ->color('primary')
                    ->label(__('easypost::livewire/carriers.accounts.actions.create.label'))
                    ->action(function () {
                        $this->mountAction('createCarrierAccount');
                    }),
            ]);
    }

    protected function getForms(): array
    {
        return [
            'createCarrierForm',
            'carrierSearchForm',
        ];
    }
}
