<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\CustomWorkflows;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\CustomWorkflow as CustomWorkflowContract;
use CybrixSolutions\EasyPost\Dto\EasyPostCredential;
use CybrixSolutions\EasyPost\Services\CarrierService;
use EasyPost\EasyPostObject;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

abstract class CustomWorkflow implements CustomWorkflowContract
{
    public function __construct(protected CarrierService $service)
    {
    }

    public function credentials(): Collection
    {
        return collect($this->service->fields()['creation_fields'] ?? [])
            ->map(function (EasyPostObject $fields) {
                return collect($fields)
                    ->map(fn (EasyPostObject $field, string $name): EasyPostCredential => new EasyPostCredential(
                        credential: $field,
                        name: $name,
                        carrierEnum: $this->service->carrierEnum(),
                        workflow: $this,
                    ));
            });
    }

    public function fieldIsRequired(string $field, EasyPostObject $credential): bool
    {
        return ! Str::contains($credential['label'], 'optional', true);
    }

    public function validationRules(): array
    {
        $rules = $this->customRules();

        if ($this->service->carrierEnum()?->needsTermsAccepted()) {
            $rules['accepted_terms'] = ['accepted'];
        }

        return $rules;
    }

    public function validationAttributes(): array
    {
        $attributes = $this->customAttributes();

        if ($this->service->carrierEnum()?->needsTermsAccepted()) {
            $attributes['accepted_terms'] = __('easypost::validation.attributes.accepted_terms');
        }

        return $attributes;
    }

    protected function customAttributes(): array
    {
        return $this->credentials()
            ->flatMap(function (Collection $fields): Collection {
                return $fields->map(fn (EasyPostCredential $credential): string => $credential->label());
            })
            ->mapWithKeys(fn (string $label, string $field): array => ["registration_data.{$field}" => $label])
            ->toArray();
    }

    protected function customRules(): array
    {
        return $this->credentials()
            ->flatMap(function (Collection $fields): Collection {
                return $fields->mapWithKeys(function (EasyPostCredential $credential): array {
                    return [$credential->name() => $credential->rulesForValidation()];
                });
            })
            ->mapWithKeys(fn (array $rules, string $field) => ["registration_data.{$field}" => $rules])
            ->toArray();
    }
}
