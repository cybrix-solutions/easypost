<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts;

use EasyPost\EasyPostObject;
use Illuminate\Support\Collection;

interface CustomWorkflow
{
    public function credentials(): Collection;

    public function fieldIsRequired(string $field, EasyPostObject $credential): bool;

    public function placeholders(): array;

    public function rulesForField(string $field): array;

    public function validationRules(): array;

    public function validationAttributes(): array;
}
