<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Models;

use CybrixSolutions\EasyPost\Concerns\CalculatesVolume;
use CybrixSolutions\EasyPost\Concerns\SortsByDimensions;
use CybrixSolutions\EasyPost\Contracts\Models\Parcel as ParcelContract;
use CybrixSolutions\EasyPost\Contracts\Models\Shipment as ShipmentContract;
use CybrixSolutions\EasyPost\Contracts\ParcelTracking\UpdateTrackingAction;
use CybrixSolutions\EasyPost\Enums\DeliveryConfirmationEnum;
use CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum;
use CybrixSolutions\EasyPost\Enums\WeightUom;
use CybrixSolutions\EasyPost\Support\DimWeightCalculator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Lang;

class Parcel extends Model implements ParcelContract
{
    use CalculatesVolume;
    use HasFactory;
    use SortsByDimensions;

    protected $casts = [
        'voided_at' => 'immutable_datetime',
        'delivered_at' => 'immutable_datetime',
        'picked_up_at' => 'immutable_datetime',
        'last_tracked_at' => 'immutable_datetime',
        'length' => 'double',
        'width' => 'double',
        'height' => 'double',
        'volume' => 'double',
        'dim_weight' => 'double',
        'weight' => 'double',
        'is_large' => 'boolean',
        'addtl_handling' => 'boolean',
        'value' => 'double',
        'delivery_confirmation' => DeliveryConfirmationEnum::class,
        'cost' => 'double',
        'is_return' => 'boolean',
        'dim_weight_divisor' => 'float',
        'weight_uom' => WeightUom::class,
    ];

    protected $touches = ['shipment'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
        $this->table = config('easypost.table_names.parcels') ?: $this->getTable();
    }

    public function dimWeightIsBilled(): bool
    {
        $actualWeight = $this->weight_uom->toPounds($this->weight);

        return ($this->dim_weight ?? 0) > $actualWeight;
    }

    public function getDimWeightDivisor(): int|float
    {
        return $this->dim_weight_divisor
            ?? $this->shipment?->carrier?->dailyRateDivisor()
            ?? 139.00;
    }

    public function isDelivered(): bool
    {
        if ($this->status?->isDelivered()) {
            return true;
        }

        return filled($this->delivered_at);
    }

    public function isPickedUp(): bool
    {
        if ($this->status?->isPickup()) {
            return true;
        }

        return filled($this->picked_up_at);
    }

    public function isVoided(): bool
    {
        return filled($this->voided_at);
    }

    public function labelWidth(): int
    {
        return 4;
    }

    public function labelHeight(): int
    {
        return 6;
    }

    /**
     * Shipment param allows us to avoid having to re-query for the shipment on certain
     * pages.
     */
    public function refreshTracking(?ShipmentContract $shipment = null): void
    {
        if ($this->isDelivered()) {
            return;
        }

        if ($shipment) {
            $this->setRelation('shipment', $shipment);
        }

        app(UpdateTrackingAction::class)($this);
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(config('easypost.models.shipment'));
    }

    public function tracking(): HasMany
    {
        return $this->hasMany(
            config('easypost.models.parcel_tracking'),
            config('easypost.table_names.parcel_tracking_parcel_fk'),
        );
    }

    public function voider(): BelongsTo
    {
        return $this->belongsTo(
            config('auth.providers.users.model'),
            'voided_by',
        );
    }

    public function canPrintLabel(): bool
    {
        return ! $this->isVoided()
            && ! $this->isPickedUp()
            && ! $this->isDelivered();
    }

    public function scopeDoesntNeedLabelFile(Builder $query): void
    {
        $query->where(function (Builder $query) {
            $query->whereNotNull('picked_up_at')
                ->orWhereNotNull('voided_at')
                ->orWhereNotNull('delivered_at');
        });
    }

    public function scopeNeedsTrackingRefreshed(Builder $query): void
    {
        $query->whereNull('delivered_at')
            ->whereNull('voided_at');
    }

    protected static function booted(): void
    {
        static::saving(function (ParcelContract $parcel) {
            if (! $parcel->exists || $parcel->isDirty(['length', 'width', 'height'])) {
                $parcel->adjustParcelDimensions();
            }
        });
    }

    protected function valueForHumans(): Attribute
    {
        return Attribute::make(
            get: fn (): string => sprintf('$%s', number_format($this->value, 2)),
        )->shouldCache();
    }

    protected function deliveredAtForHumans(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->delivered_at?->format('F j, Y g:i a'),
        )->shouldCache();
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): ?ShipmentStatusEnum {
                return $value instanceof ShipmentStatusEnum
                    ? $value
                    : ShipmentStatusEnum::tryFrom($value ?? '');
            },
        )->shouldCache();
    }

    protected function dimWeightForHumans(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Lang::choice('easypost::shipments.labels.weight_display', $this->dim_weight ?? 0, ['weight' => $this->dim_weight]),
        )->shouldCache();
    }

    protected function dimWeightHelp(): Attribute
    {
        return Attribute::make(
            get: fn (): string => __('easypost::shipments.labels.dim_weight_help', [
                'length' => $this->length,
                'width' => $this->width,
                'height' => $this->height,
                'divisor' => $this->getDimWeightDivisor(),
            ]),
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

    protected function pickedUpAtForHumans(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return is_null($this->picked_up_at)
                    ? __('easypost::shipments.tracking.alerts.not_picked_up')
                    : $this->picked_up_at->format('F j, Y');
            },
        )->shouldCache();
    }

    protected function lastTrackedAtForHumans(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return is_null($this->last_tracked_at)
                    ? __('easypost::shipments.tracking.alerts.never_tracked')
                    : $this->last_tracked_at->format('M. d, Y g:i a');
            },
        )->shouldCache();
    }

    protected function calculateDimWeight(): float
    {
        return (new DimWeightCalculator)->usingParcel($this)->dimWeight();
    }
}
