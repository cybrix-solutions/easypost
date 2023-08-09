<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\CarrierAccounts;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\ActivateCarrierAccountAction as ActivateCarrierAccountActionContract;
use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Events\CarrierAccounts\CarrierAccountWasActivated;
use Illuminate\Support\Facades\DB;

class ActivateCarrierAccountAction implements ActivateCarrierAccountActionContract
{
    public function __invoke(CarrierAccount $account): void
    {
        if ($account->isActive()) {
            return;
        }

        DB::transaction(function () use ($account) {
            $account->deactivated_at = null;
            $this->makeDefaultIfNecessary($account);

            $account->save();

            CarrierAccountWasActivated::dispatch($account);
        });
    }

    protected function makeDefaultIfNecessary(CarrierAccount $account): void
    {
        if ($account->default || app(CarrierAccount::class)::otherDefaultedAccounts($account)->active()->exists()) {
            return;
        }

        // Make other accounts not default
        app(CarrierAccount::class)::otherDefaultedAccounts($account)->update(['default' => false]);

        $account->default = true;
    }
}
