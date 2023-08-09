<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Dto;

use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\CustomWorkflow;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use EasyPost\EasyPostObject;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class EasyPostCredential
{
    public function __construct(
        protected EasyPostObject $credential,
        protected string $name,
        protected CarrierEnum $carrierEnum,
        protected bool $isTest = false, // Test env credentials need optional validation
        protected ?CustomWorkflow $workflow = null,
    ) {
    }

    public function isCheckbox(): bool
    {
        return $this->credential['visibility'] === 'checkbox';
    }

    public function isPassword(): bool
    {
        return $this->credential['visibility'] === 'password';
    }

    public function isSelect(): bool
    {
        return $this->credential['visibility'] === 'select';
    }

    public function isRequired(): bool
    {
        if ($this->isTest) {
            return false;
        }

        if ($this->workflow) {
            return $this->workflow->fieldIsRequired($this->name, $this->credential);
        }

        if ($this->isReadonly()) {
            return false;
        }

        return ! Str::contains($this->credential['label'], ['optional', '- not required'], true);
    }

    public function isReadonly(): bool
    {
        return $this->credential['visibility'] === 'readonly';
    }

    public function label(): string
    {
        return $this->credential['label'];
    }

    public function value(): mixed
    {
        if ($this->isCheckbox()) {
            return strtolower($this->credential['value'] ?? 'false') === 'true';
        }

        return $this->credential['value'] ?? '';
    }

    public function name(): string
    {
        return $this->name;
    }

    public function placeholder(): ?string
    {
        // Some custom workflows have placeholders for some fields.
        if (! $this->workflow) {
            return null;
        }

        return $this->workflow->placeholders()[$this->name] ?? null;
    }

    public function selectOptions(): array
    {
        return $this->carrierEnum->optionsFor($this->name);
    }

    public function rulesForValidation(): array
    {
        if ($this->isReadonly()) {
            return [];
        }

        $rules = [
            $this->isRequired() ? 'required' : 'nullable',
            $this->isCheckbox() ? 'boolean' : 'string',
        ];

        if ($this->isSelect()) {
            $rules[] = Rule::in(array_keys($this->selectOptions()));
        }

        if ($this->workflow) {
            $rules = array_merge($rules, $this->workflow->rulesForField($this->name));
        }

        return $rules;
    }
}
