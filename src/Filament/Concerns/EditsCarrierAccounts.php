<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Filament\Concerns;

use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Filament\Actions\EditCarrierAccountAction;
use Filament\Tables\Actions\EditAction;
use Illuminate\Support\Arr;

/**
 * @mixin \CybrixSolutions\EasyPost\Filament\Concerns\GeneratesCarrierAccountFormSchema
 * @mixin \CybrixSolutions\EasyPost\Filament\Concerns\CreatesCarrierAccounts
 */
trait EditsCarrierAccounts
{
    protected function getEditAction(): EditAction
    {
        return EditCarrierAccountAction::make()
            ->authorize('edit')
            ->form(function (CarrierAccount $record, EditCarrierAccountAction $action) {
                $carrierService = $action->getCarrierService();

                return [
                    $this->getCarrierNameField(),
                    ...Arr::wrap($this->carrierFormSchema($carrierService, isCreate: false)),
                ];
            });
    }
}
