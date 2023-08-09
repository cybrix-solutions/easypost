<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\CarrierAccounts;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\MakeCarrierDefaultAction as MakeCarrierDefaultActionContract;
use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Events\CarrierAccounts\CarrierAccountWasMadeDefault;
use Illuminate\Support\Facades\DB;

class MakeCarrierDefaultAction implements MakeCarrierDefaultActionContract
{
    public function __invoke(CarrierAccount $account): void
    {
        if ($account->default || ! $account->isActive()) {
            return;
        }

        DB::transaction(function () use ($account) {
            $this->makeOthersNotDefault($account);

            $account->update(['default' => true]);

            CarrierAccountWasMadeDefault::dispatch($account);
        });
    }

    protected function makeOthersNotDefault(CarrierAccount $account): void
    {
        app(CarrierAccount::class)::query()
            ->otherDefaultedAccounts($account)
            ->update(['default' => false]);
    }
}
