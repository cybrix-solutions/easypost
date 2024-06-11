<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Filament\Actions;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\UpdateCarrierAction;
use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountUpdateFailed;
use CybrixSolutions\EasyPost\Services\CarrierService;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\EditAction;

class EditCarrierAccountAction extends EditAction
{
    protected ?CarrierService $carrierService = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modalHeading(fn (CarrierAccount $record) => __('easypost::livewire/carriers.accounts.actions.edit.heading', ['type' => $record->type->label()]));

        $this->modalDescription(fn (CarrierAccount $record) => $record->easypost_id);

        $this->fillForm(function (CarrierAccount $record) {
            if (! $this->carrierService) {
                Notification::make()
                    ->danger()
                    ->title(__('easypost::livewire/carriers.accounts.actions.edit.account_not_found'))
                    ->persistent()
                    ->send();

                $this->cancel();
            }

            $data = [
                ...$record->attributesToArray(),
                ...$this->carrierService->storedValues(),
            ];

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            return $data;
        });

        $this->action(function (): void {
            $carrierService = $this->getCarrierService();
            if (! $carrierService) {
                Notification::make()
                    ->danger()
                    ->title(__('easypost::livewire/carriers.accounts.actions.edit.account_not_found'))
                    ->persistent()
                    ->send();

                $this->halt();
            }

            $this->process(function (array $data, CarrierAccount $record, UpdateCarrierAction $updateCarrierAction, CarrierService $carrierService) {
                $updateCarrierAction
                    ->withoutValidation()
                    ->withCarrierService($this->carrierService)
                    ->withStoredValues($this->carrierService->storedValues());

                try {
                    $updateCarrierAction($record, $data);
                } catch (CarrierAccountUpdateFailed $e) {
                    Notification::make()
                        ->danger()
                        ->title($e->getMessage())
                        ->persistent()
                        ->send();

                    $this->halt();
                }
            }, ['carrierService' => $carrierService]);

            $this->success();
        });
    }

    public function getCarrierService(): ?CarrierService
    {
        if ($this->carrierService) {
            return $this->carrierService;
        }

        return $this->carrierService = rescue(
            fn () => CarrierService::fromAccount($this->getRecord()?->easypost_id),
        );
    }
}
