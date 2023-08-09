<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\CarrierAccounts;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\DeactivateCarrierAccountAction as DeactivateCarrierAccountActionContract;
use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Events\CarrierAccounts\CarrierAccountWasDeactivated;
use Illuminate\Support\Facades\DB;

class DeactivateCarrierAccountAction implements DeactivateCarrierAccountActionContract
{
    public function __invoke(CarrierAccount $account): void
    {
        if (! $account->isActive()) {
            return;
        }

        DB::transaction(function () use ($account) {
            $account->deactivated_at = now();
            $this->makeOtherAccountDefault($account);

            $account->save();

            CarrierAccountWasDeactivated::dispatch($account);
        });
    }

    protected function makeOtherAccountDefault(CarrierAccount $account): void
    {
        if (! $account->default || ! app(CarrierAccount::class)::otherActiveAccounts($account)->exists()) {
            return;
        }

        $account->default = false;

        app(CarrierAccount::class)::otherActiveAccounts($account)->first()?->update(['default' => true]);
    }
}
