<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\CarrierAccounts;

use function CybrixSolutions\EasyPost\carrierAccountCacheKey;
use CybrixSolutions\EasyPost\Contracts\CarrierAccount as CarrierAccountContract;
use CybrixSolutions\EasyPost\Contracts\SyncCarriersAction as SyncCarriersActionContract;
use CybrixSolutions\EasyPost\Events\CarrierAccountWasCreated;
use CybrixSolutions\EasyPost\Events\CarrierAccountWasUpdated;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountSyncFailed;
use CybrixSolutions\EasyPost\Services\CarrierAccountService;
use EasyPost\Exception\Api\ApiException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SyncCarriersAction implements SyncCarriersActionContract
{
    protected array $context = [];

    /** @var callable|null */
    protected $filterBy = null;

    public function __construct(protected CarrierAccountService $api)
    {
    }

    public function __invoke(): void
    {
        try {
            $accounts = $this->api->all();
        } catch (ApiException $e) {
            throw CarrierAccountSyncFailed::because($e->getMessage());
        }

        foreach ($this->filteredAccounts($accounts) as $account) {
            cache()->forget(carrierAccountCacheKey($account->id));

            $model = app(CarrierAccountContract::class)::updateOrCreate([
                'easypost_id' => $account->id,
            ] + $this->context, [
                'type' => Str::replace('Default', '', $account->type),
                'name' => $account->description,
                'billing_type' => $account->billing_type,
            ]);

            if ($model->wasRecentlyCreated) {
                CarrierAccountWasCreated::dispatch($model, $account);
            } elseif ($model->wasChanged()) {
                CarrierAccountWasUpdated::dispatch($model);
            }
        }
    }

    public function withContext(array $context): SyncCarriersActionContract
    {
        $this->context = $context;

        return $this;
    }

    public function withAccountFilter(callable $filterBy): self
    {
        $this->filterBy = $filterBy;

        return $this;
    }

    /**
     * @param  \EasyPost\CarrierAccount[]  $accounts
     * @return \Illuminate\Support\Collection<int, \EasyPost\CarrierAccount>
     */
    protected function filteredAccounts(array $accounts): Collection
    {
        return collect($accounts)
            ->when(
                is_callable($this->filterBy),
                fn (Collection $accounts) => $accounts->filter($this->filterBy),
            );
    }
}
