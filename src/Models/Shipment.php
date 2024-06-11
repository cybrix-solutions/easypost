<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Models;

use CybrixSolutions\EasyPost\Contracts\Models\Parcel as ParcelContract;
use CybrixSolutions\EasyPost\Contracts\Models\Shipment as ShipmentContract;
use CybrixSolutions\EasyPost\Dto\ShipmentAddress;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Enums\DeliveryConfirmationEnum;
use CybrixSolutions\EasyPost\Enums\ShipmentDirectionEnum;
use CybrixSolutions\EasyPost\Enums\ShipmentRefundStatusEnum;
use CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum;
use CybrixSolutions\EasyPost\Enums\WeightUom;
use CybrixSolutions\EasyPost\Facades\EasyPost;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;
use Throwable;

class Shipment extends Model implements ShipmentContract
{
    protected function casts(): array
    {
        return [
            'is_residential' => 'boolean',
            'is_return' => 'boolean',
            'voided_at' => 'immutable_datetime',
            'delivered_at' => 'immutable_datetime',
            'picked_up_at' => 'immutable_datetime',
            'weight' => 'double',
            'delivery_confirmation' => DeliveryConfirmationEnum::class,
            'cost' => 'double',
            'sender' => ShipmentAddress::class,
            'receiver' => ShipmentAddress::class,
            'weight_uom' => WeightUom::class,
        ];
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
        $this->table = config('easypost.tables.shipments') ?: $this->getTable();
    }

    public static function findByEasyPostId(string $id): ShipmentContract
    {
        return static::query()->where('easypost_id', $id)->firstOrFail();
    }

    public function isDelivered(): bool
    {
        if ($this->getStatus()?->isDelivered()) {
            return true;
        }

        return filled($this->delivered_at);
    }

    public function isPickedUp(): bool
    {
        if ($this->getStatus()?->isPickup()) {
            return true;
        }

        return filled($this->picked_up_at)
            || $this->isDelivered();
    }

    public function isVoided(): bool
    {
        if ($this->getStatus()?->isVoid()) {
            return true;
        }

        return filled($this->voided_at);
    }

    public function isInVoidGracePeriod(): bool
    {
        return $this->voidable_until->isFuture()
            || $this->voidable_until->isToday();
    }

    public function canBeVoided(): bool
    {
        if ($this->isVoided() || $this->isPickedUp() || $this->isDelivered()) {
            return false;
        }

        return $this->isInVoidGracePeriod();
    }

    public function getUnableToVoidReason(): string
    {
        if ($this->isPickedUp() || $this->isDelivered()) {
            return __('easypost::shipments.alerts.already_picked_up_cannot_void');
        }

        if (! $this->isInVoidGracePeriod()) {
            return __('easypost::shipments.alerts.unable_to_void_outside_of_void_period');
        }

        if ($this->isVoided()) {
            return __('easypost::shipments.alerts.already_voided');
        }

        return __('easypost::shipments.alerts.unable_to_void');
    }

    public function parcels(): HasMany
    {
        return $this->hasMany(config('easypost.models.parcel'));
    }

    public function firstParcel(): HasOne
    {
        return $this->hasOne(config('easypost.models.parcel'))
            ->orderBy('id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            config('auth.providers.users.model'),
            'created_by',
        );
    }

    public function voider(): BelongsTo
    {
        return $this->belongsTo(
            config('auth.providers.users.model'),
            'voided_by',
        );
    }

    public function shippable(): MorphTo
    {
        return $this->morphTo('shippable');
    }

    public function refreshTracking(): void
    {
        $this
            ->parcels()
            ->cursor()
            ->each(function (ParcelContract $parcel) {
                if ($parcel->isDelivered()) {
                    $this->delivered_at = $parcel->delivered_at;
                    $this->signed_by = $parcel->signed_by;
                    $this->status = $parcel->status;

                    if (blank($this->picked_up_at)) {
                        $this->picked_up_at = $parcel->picked_up_at;
                    }

                    return;
                }

                rescue(fn () => $parcel->refreshTracking($this));
            });

        if ($this->isDirty()) {
            $this->save();
        }
    }

    public function scopeByStatus(Builder $query, $status): void
    {
        $statuses = collect($status)
            ->map(
                fn ($value) => rescue(fn () => ShipmentStatusEnum::tryFrom($value))
            )
            ->filter()
            ->map(fn (ShipmentStatusEnum $enum) => $enum->value);

        if ($statuses->isEmpty()) {
            return;
        }

        $query->whereIn('status', $statuses->toArray());
    }

    public function scopeInProcess(Builder $query): void
    {
        $query->whereNull('delivered_at')
            ->whereNull('voided_at');
    }

    public function scopeCompleted(Builder $query): void
    {
        $query->whereNotNull('delivered_at')
            ->orWhereNotNull('voided_at');
    }

    public function scopeByDirection(Builder $query, ?string $direction = null): void
    {
        if (! $direction) {
            return;
        }

        $enum = ShipmentDirectionEnum::tryFrom($direction);

        $query
            ->when($enum === ShipmentDirectionEnum::Forward, fn (Builder $query) => $query->where('is_return', false))
            ->when($enum === ShipmentDirectionEnum::ReturnLabel, fn (Builder $query) => $query->where('is_return', true));
    }

    public function scopeGenericSearch(Builder $query, $search = null): void
    {
        if (! $search) {
            return;
        }

        $searchableColumns = [
            'tracking_number',
            'sender',
            'receiver',
        ];

        $query->where(function (Builder $query) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $query->orWhere($column, 'LIKE', "%{$search}%");
            }
        });
    }

    protected static function booted(): void
    {
        static::creating(function (self $shipment) {
            if (! $shipment->created_by && Auth::hasUser()) {
                $shipment->created_by = EasyPost::authenticatedUserId();
            }
        });
    }

    protected function carrier(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value): ?CarrierEnum => $value instanceof CarrierEnum
                ? $value
                : CarrierEnum::tryFrom($value ?? ''),
        )->shouldCache();
    }

    protected function costForHumans(): Attribute
    {
        return Attribute::make(
            get: fn (): string => sprintf('$%s', number_format($this->cost, 2)),
        )->shouldCache();
    }

    protected function weightForHumans(): Attribute
    {
        return Attribute::make(
            get: function () {
                $weight = $this->weight_uom->toPounds($this->weight);

                return Lang::choice('easypost::shipments.labels.weight_display', $weight, ['weight' => $weight]);
            },
        )->shouldCache();
    }

    protected function direction(): Attribute
    {
        return Attribute::make(
            get: fn (): ShipmentDirectionEnum => $this->is_return ? ShipmentDirectionEnum::ReturnLabel : ShipmentDirectionEnum::Forward,
        )->shouldCache();
    }

    protected function refundStatus(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value): ?ShipmentRefundStatusEnum => ShipmentRefundStatusEnum::tryFrom($value ?? ''),
        )->shouldCache();
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?ShipmentStatusEnum => ShipmentStatusEnum::tryFrom($value ?? ''),
        )->shouldCache();
    }

    protected function getStatus(): mixed
    {
        try {
            return $this->status;
        } catch (Throwable $e) {
            return null;
        }
    }

    protected function statusColor(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->getStatus()?->getColor(),
        )->shouldCache();
    }

    protected function deliveredAtForHumans(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                return $this->delivered_at?->format('F j, Y g:i a');
            },
        )->shouldCache();
    }

    protected function pickedUpAtForHumans(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return blank($this->picked_up_at)
                    ? __('easypost::shipments.tracking.alerts.not_picked_up')
                    : $this->picked_up_at->format('F j, Y'); // March 15, 2021
            },
        )->shouldCache();
    }

    protected function voidableUntil(): Attribute
    {
        return Attribute::make(
            get: function (): DateTimeInterface {
                return $this->created_at->startOfDay()->addDays($this->carrier->voidableDays());
            },
        )->shouldCache();
    }

    protected function senderDisplay(): Attribute
    {
        return Attribute::make(
            get: function (): HtmlString {
                if ($this->is_return) {
                    return $this->receiver->asDisplay();
                }

                return $this->sender->asDisplay();
            },
        )->shouldCache();
    }

    protected function receiverDisplay(): Attribute
    {
        return Attribute::make(
            get: function (): HtmlString {
                if ($this->is_return) {
                    return $this->sender->asDisplay();
                }

                return $this->receiver->asDisplay();
            },
        )->shouldCache();
    }
}
