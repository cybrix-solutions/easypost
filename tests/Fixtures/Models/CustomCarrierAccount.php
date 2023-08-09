<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Models;

use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount as CarrierAccountContract;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\database\factories\CustomCarrierAccountFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Arr;

/**
 * @property string|null $team_id
 */
class CustomCarrierAccount extends CarrierAccount
{
    public function scopeScoped(Builder|QueryBuilder $query, CarrierAccountContract $account): void
    {
        $query->when(
            $account->team_id,
            fn ($query) => $query->where('team_id', $account->team_id),
            fn ($query) => $query->whereNull('team_id'),
        );
    }

    public function scopeShouldBeDefaultFromContext(Builder $query, array $context): void
    {
        $teamId = Arr::get($context, 'team_id');

        $query->when(
            $teamId,
            fn (Builder $query) => $query->where('team_id', $teamId),
            fn (Builder $query) => $query->whereNull('team_id'),
        );
    }

    public function scopeNewAccountUniqueValidationFromContext(Builder|QueryBuilder $query, array $context): void
    {
        $teamId = Arr::get($context, 'team_id');

        $query->when(
            $teamId,
            fn (Builder|QueryBuilder $query) => $query->where('team_id', $teamId)->orWhereNull('team_id'),
        );
    }

    protected static function newFactory(): CustomCarrierAccountFactory
    {
        return CustomCarrierAccountFactory::new();
    }
}
