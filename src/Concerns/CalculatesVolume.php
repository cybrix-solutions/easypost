<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * @property float $length
 * @property float $width
 * @property float $height
 * @property string $dimensions
 * @property float $volume
 * @property float $dim_weight
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait CalculatesVolume
{
    abstract protected function calculateDimWeight(): float;

    public static function bootCalculatesVolume(): void
    {
        static::saving(function (Model $model) {
            /** @var \CybrixSolutions\EasyPost\Concerns\CalculatesVolume $model */
            if (! $model->exists || $model->isDirty(['length', 'width', 'height'])) {
                $model->adjustParcelDimensions();
            }
        });
    }

    public function adjustParcelDimensions(): void
    {
        $this->volume = $this->calculateVolume();
        $this->dimensions = sprintf('%sx%sx%s', $this->length, $this->width, $this->height);
        $this->dim_weight = $this->calculateDimWeight();
    }

    public function calculateVolume(): float
    {
        return $this->length * $this->width * $this->height;
    }
}
