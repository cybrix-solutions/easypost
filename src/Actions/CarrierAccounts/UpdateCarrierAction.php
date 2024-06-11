<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\CarrierAccounts;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\UpdateCarrierAction as UpdateCarrierActionContract;
use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Events\CarrierAccounts\CarrierAccountWasUpdated;
use CybrixSolutions\EasyPost\Services\CarrierAccountService;
use CybrixSolutions\EasyPost\Services\CarrierService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function CybrixSolutions\EasyPost\carrierAccountCacheKey;

class UpdateCarrierAction implements UpdateCarrierActionContract
{
    protected CarrierService $carrierService;

    protected array $storedValues = [];

    protected bool $validateData = true;

    public function __construct(protected CarrierAccountService $api)
    {
    }

    public function __invoke(CarrierAccount $account, array $input): CarrierAccount
    {
        $data = $this->validate($account, $input);

        $this->updateAccountInApi($account, $data);

        cache()->forget(carrierAccountCacheKey($account->easypost_id));

        return tap($account, function (CarrierAccount $account) use ($data) {
            DB::transaction(function () use ($account, $data) {
                $account->update(['name' => $data['name']]);

                CarrierAccountWasUpdated::dispatch($account);
            });
        });
    }

    public function withCarrierService(CarrierService $carrierService): UpdateCarrierActionContract
    {
        $this->carrierService = $carrierService;

        return $this;
    }

    public function withStoredValues(array $values): UpdateCarrierActionContract
    {
        $this->storedValues = $values;

        return $this;
    }

    public function withoutValidation(): UpdateCarrierActionContract
    {
        $this->validateData = false;

        return $this;
    }

    protected function updateAccountInApi(CarrierAccount $account, array $data): void
    {
        $this->api->update(
            $account->easypost_id,
            [
                'description' => $data['name'],
                ...$this->changedValues(Arr::except($data, 'name')),
            ],
        );
    }

    protected function validate(CarrierAccount $account, array $input): array
    {
        if (! $this->validateData) {
            return $input;
        }

        return Validator::make(data: $input, rules: [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                Rule::unique(app(CarrierAccount::class)::class, 'name')
                    ->where(fn ($query) => app(CarrierAccount::class)->scopeScoped($query, $account))
                    ->ignoreModel($account),
            ],
            ...$this->carrierService->rulesForValidation(),
        ], attributes: $this->carrierService->validationAttributes())->validate();
    }

    /**
     * Updates sent to the API are partial updates, so we should only send
     * the values that have changed.
     */
    protected function changedValues(array $input): array
    {
        $changes = array_diff(
            $this->flattenValues($input),
            $this->flattenValues($this->storedValues),
        );

        $changes = array_filter(
            $changes,
            fn (string $key) => ! in_array($key, $this->carrierService->readonlyFields(), true),
            ARRAY_FILTER_USE_KEY,
        );

        return Arr::undot($changes);
    }

    protected function flattenValues(array $values): array
    {
        return array_filter(Arr::dot($values), fn ($value) => ! is_array($value));
    }
}
