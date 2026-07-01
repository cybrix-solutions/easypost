<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Filament\Actions;

use Closure;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\SyncCarriersAction;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountSyncFailed;
use Filament\Actions\Action;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentIcon;

use function CybrixSolutions\EasyPost\hasApiKey;

class SyncCarrierAccountsAction extends Action
{
    use CanCustomizeProcess;

    protected array|Closure|null $context = null;

    protected ?Closure $filterAccountsBy = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('easypost::livewire/carriers.accounts.actions.sync.label'));

        $this->link();

        $this->requiresConfirmation();

        $this->authorize('sync', config('easypost.models.carrier_account'));

        $this->modalIcon(FilamentIcon::resolve('easypost::sync') ?? 'heroicon-m-arrow-path');

        $this->modalDescription(__('easypost::livewire/carriers.accounts.actions.sync.description'));

        $this->modalSubmitActionLabel(__('easypost::livewire/carriers.accounts.actions.sync.modal_submit'));

        $this->successNotificationTitle(__('easypost::livewire/carriers.accounts.actions.sync.success'));

        $this->action(function (): void {
            $this->process(function (SyncCarriersAction $syncCarriersAction) {
                if (! hasApiKey()) {
                    return;
                }

                $syncCarriersAction
                    ->withContext($this->getContext())
                    ->filterAccountsWith($this->getFilterAccountsBy());

                try {
                    $syncCarriersAction();
                } catch (CarrierAccountSyncFailed $e) {
                    Notification::make()
                        ->danger()
                        ->title($e->getMessage())
                        ->persistent()
                        ->send();

                    $this->halt();
                }
            });

            $this->success();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'sync';
    }

    public function withContext(array|Closure|null $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function filterAccountsBy(?Closure $callback): static
    {
        $this->filterAccountsBy = $callback;

        return $this;
    }

    public function getContext(): array
    {
        return $this->evaluate($this->context) ?? [];
    }

    public function getFilterAccountsBy(): ?Closure
    {
        return $this->filterAccountsBy;
    }
}
