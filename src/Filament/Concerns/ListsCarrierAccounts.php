<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Filament\Concerns;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\ActivateCarrierAccountAction;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\DeactivateCarrierAccountAction;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\DeleteCarrierAction;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\MakeCarrierDefaultAction;
use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountDeletionFailed;
use CybrixSolutions\EasyPost\Filament\Actions\SyncCarrierAccountsAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

trait ListsCarrierAccounts
{
    protected function getCarrierTableColumns(): array
    {
        return [
            $this->getAccountColumn(),
            $this->getTypeColumn(),
            $this->getStatusColumn(),
        ];
    }

    protected function getCarrierTableActions(): array
    {
        return [
            ActionGroup::make([
                $this->getEditAction(),
                $this->getMakeDefaultAction(),
                $this->getActivateAction(),
                $this->getDeactivateAction(),
                $this->getDeleteAction(),
            ]),
        ];
    }

    protected function getCarrierTableHeaderActions(): array
    {
        return [
            $this->getSyncCarriersAction(),
        ];
    }

    protected function getAccountColumn(): Column
    {
        return ViewColumn::make('name')
            ->label(__('easypost::livewire/carriers.accounts.table.name.label'))
            ->searchable(['name', 'easypost_id', 'type'])
            ->view('easypost::filament.tables.carriers.account-column');
    }

    protected function getTypeColumn(): Column
    {
        return TextColumn::make('carrier')
            ->label(__('easypost::livewire/carriers.accounts.table.carrier.label'))
            ->default(fn (CarrierAccount $record) => $record->type->label());
    }

    protected function getStatusColumn(): Column
    {
        return TextColumn::make('status')
            ->default(fn (CarrierAccount $record) => $record->isActive())
            ->formatStateUsing(fn (bool $state) => $state ? __('easypost::labels.carrier_account.is_active') : __('easypost::labels.carrier_account.is_inactive'))
            ->color(fn (bool $state) => $state ? 'success' : 'danger')
            ->description(function (CarrierAccount $record) {
                if (! $record->default) {
                    return null;
                }

                return new HtmlString(Blade::render(<<<'BLADE'
                <div class="inline-flex">
                    <x-filament::badge color="primary">{{ __('easypost::labels.carrier_account.is_default') }}</x-filament::badge>
                </div>
                BLADE));
            })
            ->badge();
    }

    protected function getMakeDefaultAction(): Action
    {
        return Action::make('makeDefault')
            ->authorize('makeDefault')
            ->label(__('easypost::livewire/carriers.accounts.actions.make_default.label'))
            ->icon(FilamentIcon::resolve('easypost::default') ?? 'heroicon-o-check-badge')
            ->action(function (CarrierAccount $record, MakeCarrierDefaultAction $makeCarrierDefaultAction) {
                $makeCarrierDefaultAction($record);

                Notification::make()
                    ->success()
                    ->title(__('easypost::livewire/carriers.accounts.actions.make_default.success', ['name' => $record->name]))
                    ->send();
            });
    }

    protected function getActivateAction(): Action
    {
        return Action::make('activate')
            ->authorize('activate')
            ->label(__('easypost::livewire/carriers.accounts.actions.activate.label'))
            ->icon(FilamentIcon::resolve('easypost::activate') ?? 'heroicon-o-eye')
            ->action(function (CarrierAccount $record, ActivateCarrierAccountAction $activateCarrierAccountAction) {
                $activateCarrierAccountAction($record);

                Notification::make()
                    ->success()
                    ->title(__('easypost::livewire/carriers.accounts.actions.activate.success', ['name' => $record->name]))
                    ->send();
            });
    }

    protected function getDeactivateAction(): Action
    {
        return Action::make('deactivate')
            ->authorize('deactivate')
            ->label(__('easypost::livewire/carriers.accounts.actions.deactivate.label'))
            ->icon(FilamentIcon::resolve('easypost::deactivate') ?? 'heroicon-o-eye-slash')
            ->action(function (CarrierAccount $record, DeactivateCarrierAccountAction $deactivateCarrierAccountAction) {
                $deactivateCarrierAccountAction($record);

                Notification::make()
                    ->success()
                    ->title(__('easypost::livewire/carriers.accounts.actions.deactivate.success', ['name' => $record->name]))
                    ->send();
            });
    }

    protected function getDeleteAction(): Action
    {
        return DeleteAction::make()
            ->modalWidth(MaxWidth::ExtraLarge)
            ->modalHeading(__('easypost::livewire/carriers.accounts.actions.delete.heading'))
            ->authorize('delete')
            ->modalDescription(fn (CarrierAccount $record): Htmlable => new HtmlString(Blade::render(<<<'BLADE'
            <div class="text-left fi-modal-description space-y-3 text-sm text-gray-500 dark:texts-gray-400">
                {{ new \Illuminate\Support\HtmlString(Str::markdown(__('easypost::livewire/carriers.accounts.actions.delete.description', ['carrier' => $record->type->label(), 'id' => $record->easypost_id]))) }}

                {{ new \Illuminate\Support\HtmlString(Str::markdown(__('easypost::livewire/carriers.accounts.actions.delete.warning'))) }}
            </div>
            BLADE, ['record' => $record])))
            ->modalSubmitActionLabel(__('filament-actions::delete.single.modal.actions.delete.label'))
            ->using(function (CarrierAccount $record, Action $action, DeleteCarrierAction $deleteCarrierAction) {
                try {
                    $deleteCarrierAction($record);
                } catch (CarrierAccountDeletionFailed $e) {
                    Notification::make()
                        ->danger()
                        ->title($e->getMessage())
                        ->persistent()
                        ->send();

                    $action->halt();
                }

                return true;
            });
    }

    protected function getSyncCarriersAction(): Action|SyncCarrierAccountsAction
    {
        return SyncCarrierAccountsAction::make()
            ->disabled(fn (): bool => ! $this->hasProductionApiKey);
    }
}
