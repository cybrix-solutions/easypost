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

    protected function casts(): array
    {
        return [
            'activity_date' => 'immutable_datetime',
        ];
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
        $this->table = config('easypost.table_names.parcel_tracking') ?: $this->getTable();
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
}
