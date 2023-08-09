<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\CarrierAccounts;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\DeleteCarrierAction as DeleteCarrierActionContract;
use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Events\CarrierAccounts\CarrierAccountWasDeleted;
use CybrixSolutions\EasyPost\Services\CarrierAccountService;
use Illuminate\Support\Facades\DB;

class DeleteCarrierAction implements DeleteCarrierActionContract
{
    public function __construct(protected CarrierAccountService $api)
    {
    }

    public function __invoke(CarrierAccount $account): void
    {
        $this->api->destroy($account->easypost_id);

        DB::transaction(function () use ($account) {
            $account->delete();

            $this->makeOtherAccountDefaultIfNecessary($account);

            CarrierAccountWasDeleted::dispatch($account);
        });
    }

    protected function makeOtherAccountDefaultIfNecessary(CarrierAccount $account): void
    {
        if (! $account->default || ! app(CarrierAccount::class)::otherActiveAccounts($account)->exists()) {
            return;
        }

        app(CarrierAccount::class)::otherActiveAccounts($account)->first()?->update(['default' => true]);
    }
}
