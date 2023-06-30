<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Models;

use CybrixSolutions\EasyPost\Contracts\CarrierAccount as CarrierAccountContract;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\database\factories\CustomCarrierAccountFactory;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property string|null $team_id
 */
class CustomCarrierAccount extends CarrierAccount
{
    public function scopeScoped(Builder $query, CarrierAccountContract $account): void
    {
        $query->when(
            $account->team_id,
            fn ($query) => $query->where('team_id', $account->team_id),
            fn ($query) => $query->whereNull('team_id'),
        );
    }

    protected static function newFactory(): CustomCarrierAccountFactory
    {
        return CustomCarrierAccountFactory::new();
    }
}
