<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Support;

use CybrixSolutions\EasyPost\Contracts\Models\Parcel;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;

class DimWeightCalculator
{
    protected ?CarrierEnum $carrierType = null;

    protected ?Parcel $parcel = null;

    protected float $defaultDimWeightDivisor = 139;

    public function __construct(
        protected float $length = 0,
        protected float $height = 0,
        protected float $width = 0,
    ) {}

    public function usingCarrierType(CarrierEnum $carrierType): self
    {
        $this->carrierType = $carrierType;

        return $this;
    }

    public function usingParcel(Parcel $parcel): self
    {
        $this->parcel = $parcel;

        $this->length = $parcel->length ?? 0;
        $this->height = $parcel->height ?? 0;
        $this->width = $parcel->width ?? 0;

        return $this;
    }

    public function usingLength(float $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function usingHeight(float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function usingWidth(float $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function usingDimWeightDivisor(float $divisor): self
    {
        $this->defaultDimWeightDivisor = $divisor;

        return $this;
    }

    public function volume(): float
    {
        return $this->length * $this->width * $this->height;
    }

    public function dimWeight(): float
    {
        return ceil($this->volume() / $this->divisor());
    }

    protected function divisor(): int|float
    {
        if ($this->parcel) {
            return $this->parcel->getDimWeightDivisor();
        }

        if ($this->carrierType) {
            return $this->carrierType->dailyRateDivisor();
        }

        return $this->defaultDimWeightDivisor;
    }
}
