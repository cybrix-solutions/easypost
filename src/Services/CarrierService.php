<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services;

use CybrixSolutions\EasyPost\CustomWorkflows\Factory as WorkflowFactory;
use CybrixSolutions\EasyPost\Dto\EasyPostCredential;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Services\Api\ProductionEasyPostClient;
use EasyPost\CarrierAccount;
use EasyPost\EasyPostObject;
use EasyPost\Util\InternalUtil as EasyPostUtil;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

use function CybrixSolutions\EasyPost\carrierAccountCacheKey;
use function CybrixSolutions\EasyPost\easypostObjectToArray;

/**
 * @method string|null signupHelpUrl()
 * @method string|null signupInstructions()
 * @method string|null signupText()
 * @method string|null signupUrl()
 */
final class CarrierService
{
    protected ?CarrierEnum $enum;

    protected ?EasyPostObject $_fields = null;

    protected ?bool $_customWorkflow = null;

    protected ?bool $_hasTestCredentials = null;

    public function __construct(protected EasyPostObject|CarrierAccount $carrier)
    {
        $this->enum = CarrierEnum::tryFrom($carrier->type);
    }

    public function __call(string $name, array $arguments)
    {
        if (! $this->enum) {
            return null;
        }

        if (method_exists($this->enum, $name)) {
            return $this->enum->{$name}(...$arguments);
        }

        return null;
    }

    public static function fromType(string|CarrierEnum $type): self
    {
        if ($type instanceof CarrierEnum) {
            $type = $type->value;
        }

        $carrierType = self::types()
            ->filter(fn (EasyPostObject $carrier) => $carrier['type'] === $type)
            ->firstOrFail();

        return new self($carrierType);
    }

    /**
     * @return Collection<int, string>
     */
    public static function availableTypes(): Collection
    {
        return self::types()
            ->pluck('type')
            ->filter()
            ->values();
    }

    /**
     * @return Collection<int, EasyPostObject>
     */
    protected static function types(): Collection
    {
        $types = cache()->remember(
            key: config('easypost.cache.carriers.key'),
            ttl: config('easypost.cache.carriers.ttl', 60 * 60 * 24), // 24 hours
            callback: function () {
                $types = app(ProductionEasyPostClient::class)->carrierAccount->types();

                return Arr::map($types, fn (EasyPostObject $type) => easypostObjectToArray($type));
            },
        );

        return collect(EasyPostUtil::convertToEasyPostObject(client: null, response: $types));
    }

    public static function fromAccount(string $easypostId): self
    {
        $carrier = cache()->remember(
            key: carrierAccountCacheKey($easypostId),
            ttl: config('easypost.cache.carrier_account.ttl', \DateInterval::createFromDateString('1 month')),
            callback: function () use ($easypostId) {
                $account = app(ProductionEasyPostClient::class)->carrierAccount->retrieve($easypostId);

                return easypostObjectToArray($account);
            },
        );

        return new self(EasyPostUtil::convertToEasyPostObject(null, $carrier));
    }

    public function carrierEnum(): ?CarrierEnum
    {
        return $this->enum;
    }

    public function fields(): EasyPostObject
    {
        return $this->_fields ?? ($this->_fields = $this->carrier['fields']);
    }

    public function isCustomWorkflow(): bool
    {
        if (is_bool($this->_customWorkflow)) {
            return $this->_customWorkflow;
        }

        return $this->_customWorkflow = $this->fields()['custom_workflow'] ?? false;
    }

    /**
     * @return Collection<string, EasyPostCredential>
     */
    public function productionCredentials(): Collection
    {
        return collect($this->fields()['credentials'] ?? [])
            ->map(fn (EasyPostObject $credential, string $name) => new EasyPostCredential(
                credential: $credential,
                name: $name,
                carrierEnum: $this->enum
            ));
    }

    /**
     * @return Collection<string, EasyPostCredential>
     */
    public function testCredentials(): Collection
    {
        return collect($this->fields()['test_credentials'] ?? [])
            ->map(fn (EasyPostObject $credential, string $name) => new EasyPostCredential(
                credential: $credential,
                name: $name,
                carrierEnum: $this->enum,
                isTest: true,
            ));
    }

    /**
     * @return Collection<string, EasyPostCredential>
     */
    public function customCredentials(): Collection
    {
        return WorkflowFactory::make($this)->credentials();
    }

    public function hasTestCredentials(): bool
    {
        return $this->_hasTestCredentials ?? ($this->_hasTestCredentials = isset($this->fields()['test_credentials']));
    }

    public function storedValues(): array
    {
        return [
            'credentials' => $this->sectionStoredValues($this->productionCredentials()),
            'test_credentials' => $this->sectionStoredValues($this->testCredentials()),
        ];
    }

    public function readonlyFields(): array
    {
        return [
            ...$this->sectionReadonlyFields($this->productionCredentials(), 'credentials'),
            ...$this->sectionReadonlyFields($this->testCredentials(), 'test_credentials'),
        ];
    }

    public function rulesForValidation(): array
    {
        if ($this->isCustomWorkflow()) {
            return WorkflowFactory::make($this)->validationRules();
        }

        $productionRules = $this->productionCredentials()
            ->mapWithKeys(function (EasyPostCredential $credential, string $key): array {
                return ["credentials.{$key}" => $credential->rulesForValidation()];
            })
            ->toArray();

        $testRules = $this->testCredentials()
            ->mapWithKeys(function (EasyPostCredential $credential, string $key): array {
                return ["test_credentials.{$key}" => $credential->rulesForValidation()];
            })
            ->toArray();

        return [
            ...$productionRules,
            ...$testRules,
        ];
    }

    public function validationAttributes(): array
    {
        if ($this->isCustomWorkflow()) {
            return WorkflowFactory::make($this)->validationAttributes();
        }

        $productionAttributes = $this->productionCredentials()
            ->mapWithKeys(fn (EasyPostCredential $credential, string $key): array => ["credentials.{$key}" => $credential->label()])
            ->toArray();

        $testAttributes = $this->testCredentials()
            ->mapWithKeys(fn (EasyPostCredential $credential, string $key): array => ["test_credentials.{$key}" => $credential->label()])
            ->toArray();

        return [
            ...$productionAttributes,
            ...$testAttributes,
        ];
    }

    private function sectionReadonlyFields(Collection $credentials, string $fieldSection): array
    {
        return $credentials
            ->filter(fn (EasyPostCredential $credential) => $credential->isReadonly())
            ->map(fn (EasyPostCredential $credential) => "{$fieldSection}.{$credential->name()}")
            ->values()
            ->toArray();
    }

    private function sectionStoredValues(Collection $credentials): array
    {
        return $credentials
            ->mapWithKeys(fn (EasyPostCredential $credential, string $name): array => [$name => $credential->value()])
            ->toArray();
    }
}
