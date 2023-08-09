<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Models;

use CybrixSolutions\EasyPost\Contracts\Models\ParcelTracking as ParcelTrackingContract;
use CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParcelTracking extends Model implements ParcelTrackingContract
{
    use HasFactory;
    use MassPrunable;

    protected $casts = [
        'activity_date' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
        $this->table = config('easypost.table_names.parcel_tracking') ?: $this->getTable();
    }

    protected function shipmentStatus(): Attribute
    {
        return Attribute::make(
            get: fn (): ?ShipmentStatusEnum => $this->status_code instanceof ShipmentStatusEnum
                ? $this->status_code
                : ShipmentStatusEnum::tryFrom($this->status_code ?? ''),
        )->shouldCache();
    }

    protected function locationDisplay(): Attribute
    {
        return Attribute::make(
            get: fn (): string => implode(', ', array_filter([
                $this->city,
                $this->state,
            ])),
        )->shouldCache();
    }

    protected function statusTextColor(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return match ($this->shipment_status) {
                    ShipmentStatusEnum::Delivered => 'text-green-600 dark:text-green-400',
                    ShipmentStatusEnum::OutForDelivery, ShipmentStatusEnum::PreTransit => 'text-blue-600 dark:text-blue-400',
                    ShipmentStatusEnum::Cancelled, ShipmentStatusEnum::Failure, ShipmentStatusEnum::Error => 'text-red-600 dark:text-red-400',
                    default => 'text-slate-600 dark:text-slate-300',
                };
            },
        )->shouldCache();
    }

    protected function iconCss(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return match ($this->shipment_status) {
                    ShipmentStatusEnum::Delivered => 'bg-green-600 dark:bg-green-800 text-gray-100 dark:text-green-200',
                    ShipmentStatusEnum::OutForDelivery, ShipmentStatusEnum::PreTransit => 'text-blue-600 border border-blue-600 bg-white dark:bg-blue-800 dark:text-blue-200 dark:border-blue-800',
                    ShipmentStatusEnum::Cancelled, ShipmentStatusEnum::Failure, ShipmentStatusEnum::Error => 'text-red-600 border border-red-600 bg-white dark:bg-red-800 dark:text-red-200 dark:border-red-800',
                    default => 'text-slate-600 bg-white border border-slate-600 dark:bg-slate-600 dark:text-slate-200',
                };
            },
        )->shouldCache();
    }

    protected function icon(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                return match ($this->shipment_status) {
                    ShipmentStatusEnum::Delivered => 'heroicon-o-hand-thumb-up',
                    ShipmentStatusEnum::OutForDelivery => 'css-time',
                    ShipmentStatusEnum::PreTransit => 'heroicon-o-cursor-arrow-ripple',
                    ShipmentStatusEnum::Cancelled => 'heroicon-o-no-symbol',
                    ShipmentStatusEnum::Error, ShipmentStatusEnum::Failure => 'css-bell',
                    ShipmentStatusEnum::ReturnToSender => 'heroicon-s-arrow-uturn-left',
                    default => 'heroicon-o-archive-box',
                };
            },
        )->shouldCache();
    }

    public function parcel(): BelongsTo
    {
        return $this->belongsTo(
            config('easypost.models.parcel'),
            config('easypost.table_names.parcel_tracking_parcel_fk'),
        );
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subYears(2));
    }
}
