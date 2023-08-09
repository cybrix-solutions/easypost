<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\CarrierAccounts;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\AddCarrierAccountAction as AddCarrierAccountActionContract;
use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Events\CarrierAccounts\CarrierAccountWasCreated;
use CybrixSolutions\EasyPost\Services\CarrierAccountService;
use CybrixSolutions\EasyPost\Services\CarrierService;
use EasyPost\CarrierAccount as EasyPostCarrierAccount;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AddCarrierAccountAction implements AddCarrierAccountActionContract
{
    protected CarrierService $carrierService;

    protected array $context = [];

    protected ?string $reference = null;

    public function __construct(protected CarrierAccountService $api)
    {
    }

    public function __invoke(array $input): CarrierAccount
    {
        $data = $this->validate($input);

        $account = $this->createAccountInApi($data);

        return tap(app(CarrierAccount::class)::make(), function (CarrierAccount $model) use ($account) {
            DB::beginTransaction();

            $model->fill([
                'easypost_id' => $account->id,
                'name' => $account->description,
                'type' => $this->carrierService->carrierEnum()->value,
                'default' => $this->shouldBeDefaultedAccount(),
                ...$this->context,
            ])->save();

            CarrierAccountWasCreated::dispatch($model, $account, $this->reference);

            DB::commit();
        });
    }

    public function withCarrierService(CarrierService $service): AddCarrierAccountActionContract
    {
        $this->carrierService = $service;

        return $this;
    }

    public function withContext(array $context): AddCarrierAccountActionContract
    {
        $this->context = $context;

        return $this;
    }

    public function withReference(?string $reference): AddCarrierAccountActionContract
    {
        $this->reference = $reference;

        return $this;
    }

    protected function createAccountInApi(array $data): EasyPostCarrierAccount
    {
        return $this->api->create(
            type: $this->carrierService->carrierEnum()->value,
            name: $data['name'],
            data: Arr::except($data, ['name', 'accepted_terms']),
            reference: $this->reference,
        );
    }

    protected function validate(array $input): array
    {
        return Validator::make(data: $input, rules: [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                Rule::unique(app(CarrierAccount::class)::class, 'name')
                    ->where(fn ($query) => app(CarrierAccount::class)->scopeNewAccountUniqueValidationFromContext($query, $this->context)),
            ],
            ...$this->carrierService->rulesForValidation(),
        ], attributes: $this->carrierService->validationAttributes())->validate();
    }

    protected function shouldBeDefaultedAccount(): bool
    {
        return ! app(CarrierAccount::class)::query()
            ->active()
            ->where('default', true)
            ->shouldBeDefaultFromContext($this->context)
            ->exists();
    }
}
