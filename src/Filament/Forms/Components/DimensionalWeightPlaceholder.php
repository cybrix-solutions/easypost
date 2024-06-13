<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Filament\Forms\Components;

use Closure;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Support\DimWeightCalculator;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\Lang;

class DimensionalWeightPlaceholder extends Placeholder
{
    protected int|float|null|Closure $length = null;

    protected int|float|null|Closure $width = null;

    protected int|float|null|Closure $height = null;

    protected null|CarrierEnum|Closure $carrier = null;

    protected bool $showHelperText = true;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('easypost::shipments.labels.dim_weight'));

        $this->content(function () {
            $dimWeight = $this->calculateDimensionalWeight();

            return Lang::choice('easypost::shipments.labels.weight_display', (int) $dimWeight, ['weight' => $dimWeight]);
        });

        $this->helperText(function () {
            if (! $this->shouldShowHelperText()) {
                return null;
            }

            return __('easypost::shipments.labels.dim_weight_help', [
                'length' => $this->getLength(),
                'width' => $this->getWidth(),
                'height' => $this->getHeight(),
                'divisor' => $this->getCarrier()->dailyRateDivisor(),
            ]);
        });
    }

    public function setLength(int|float|null|Closure $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function setWidth(int|float|null|Closure $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function setHeight(int|float|null|Closure $height): static
    {
        $this->height = $height;

        return $this;
    }

    public function setCarrier(null|CarrierEnum|Closure $carrier): static
    {
        $this->carrier = $carrier;

        return $this;
    }

    public function showHelperText(bool $condition = true): static
    {
        $this->showHelperText = $condition;

        return $this;
    }

    public function getLength(): float
    {
        return once(
            fn () => (float) $this->evaluate($this->length ?? 0)
        );
    }

    public function getWidth(): float
    {
        return once(
            fn () => (float) $this->evaluate($this->width ?? 0)
        );
    }

    public function getHeight(): float
    {
        return once(
            fn () => (float) $this->evaluate($this->height ?? 0)
        );
    }

    public function getCarrier(): CarrierEnum
    {
        return once(
            fn () => $this->evaluate($this->carrier)
        );
    }

    public function shouldShowHelperText(): bool
    {
        return $this->showHelperText;
    }

    protected function calculateDimensionalWeight(): float
    {
        return (new DimWeightCalculator)
            ->usingCarrierType($this->getCarrier())
            ->usingLength($this->getLength())
            ->usingWidth($this->getWidth())
            ->usingHeight($this->getHeight())
            ->dimWeight();
    }
}
