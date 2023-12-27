<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Models;

use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount as CarrierAccountContract;
use CybrixSolutions\EasyPost\Database\Factories\CarrierAccountFactory;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * CybrixSolutions\EasyPost\Models\CarrierAccount
 *
 * @property int $id
 * @property string $name
 * @property string $easypost_id
 * @property string $billing_type
 * @property bool $default
 * @property \Carbon\CarbonImmutable|null $deactivated_at
 * @property \CybrixSolutions\EasyPost\Enums\CarrierEnum $type CarrierEnum
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 *
 * @method static Builder|CarrierAccount active()
 * @method static Builder|CarrierAccount newModelQuery()
 * @method static Builder|CarrierAccount newQuery()
 * @method static Builder|CarrierAccount otherActiveAccounts(\self $account)
 * @method static Builder|CarrierAccount otherDefaultedAccounts(\self $account)
 * @method static Builder|CarrierAccount otherInactiveAccounts(\self $account)
 * @method static Builder|CarrierAccount shouldBeDefaultFromContext(array $context)
 * @method static Builder|CarrierAccount newAccountUniqueValidationFromContext(array $context)
 * @method static Builder|CarrierAccount query()
 *
 * @mixin \Eloquent
 */
class CarrierAccount extends Model implements CarrierAccountContract
{
    use HasFactory;

    protected $casts = [
        'type' => CarrierEnum::class,
        'default' => 'boolean',
        'deactivated_at' => 'immutable_datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
        $this->table = config('easypost.table_names.carrier_accounts') ?: $this->getTable();
    }

    public static function findByEasyPostId(string $id): self
    {
        return static::query()
            ->where('easypost_id', $id)
            ->firstOrFail();
    }

    public function isActive(): bool
    {
        return blank($this->deactivated_at);
    }

    public function isEasyPostAccount(): bool
    {
        return $this->billing_type === 'easypost';
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereNull('deactivated_at');
    }

    /**
     * Scope the query to find other inactive carrier accounts within the scope of the given account.
     */
    public function scopeOtherInactiveAccounts(Builder $query, CarrierAccountContract $account): void
    {
        $query
            ->scoped($account)
            ->whereNotNull('deactivated_at')
            ->whereKeyNot($account->id);
    }

    /**
     * Scope the query to find other accounts marked as "default" within the scope of the given account.
     */
    public function scopeOtherDefaultedAccounts(Builder $query, CarrierAccountContract $account): void
    {
        $query
            ->scoped($account)
            ->where('default', true)
            ->whereKeyNot($account->id);
    }

    /**
     * Scope the query to find other active carrier accounts within the scope of the given account.
     */
    public function scopeOtherActiveAccounts(Builder $query, CarrierAccountContract $account): void
    {
        $query
            ->scoped($account)
            ->whereNull('deactivated_at')
            ->whereKeyNot($account->id);
    }

    /**
     * This scope is meant to be overridden in a child class to be able to scope the query based
     * on their application needs.
     *
     * @example An application has teams and each team has their own carrier accounts.
     *     -> $query->where('team_id', $account->team_id);
     */
    public function scopeScoped(Builder|QueryBuilder $query, CarrierAccountContract $account): void
    {
    }

    /**
     * This scope is meant to be overridden in a child class to be able to scope the query based on
     * an array of custom context given to an action class for determining if the new account should
     * be marked as default.
     */
    public function scopeShouldBeDefaultFromContext(Builder $query, array $context): void
    {
    }

    /**
     * This scope is meant to be overridden in a child class to be able to scope the query based on
     * an array of custom context given to an action class for determining if the new account has
     * a unique name.
     */
    public function scopeNewAccountUniqueValidationFromContext(Builder|QueryBuilder $query, array $context): void
    {
    }

    // We are purposely not declaring a return type here so child classes can override this if necessary.
    protected static function newFactory()
    {
        return CarrierAccountFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (self $account): void {
            if (! $account->default) {
                return;
            }

            // Make other accounts in the same scope not default.
            static::otherDefaultedAccounts($account)->update(['default' => false]);
        });
    }
}
