<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * @property int $id
 * @property string $name
 * @property string $easypost_id
 * @property string $billing_type
 * @property bool $default
 * @property \Carbon\CarbonImmutable|null $deactivated_at
 * @property \CybrixSolutions\EasyPost\Enums\CarrierEnum $type CarrierEnum
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 */
interface CarrierAccount
{
    public static function findByEasyPostId(string $id): self;

    public function isActive(): bool;

    public function isEasyPostAccount(): bool;

    public function scopeActive(Builder $query): void;

    public function scopeOtherInactiveAccounts(Builder $query, self $account): void;

    public function scopeOtherDefaultedAccounts(Builder $query, self $account): void;

    public function scopeOtherActiveAccounts(Builder $query, self $account): void;

    public function scopeScoped(Builder|QueryBuilder $query, self $account): void;

    public function scopeShouldBeDefaultFromContext(Builder $query, array $context): void;

    public function scopeNewAccountUniqueValidationFromContext(Builder|QueryBuilder $query, array $context): void;
}
