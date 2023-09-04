<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Dto;

use ArrayAccess;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Enums\LengthUom;
use CybrixSolutions\EasyPost\Enums\WeightUom;
use CybrixSolutions\EasyPost\Exceptions\InvalidUom;
use CybrixSolutions\EasyPost\Support\DimWeightCalculator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use JsonSerializable;

/**
 * @property string $uuid
 * @property null|float $length
 * @property null|float $width
 * @property null|float $height
 * @property null|float $weight
 * @property null|float $return_length
 * @property null|float $return_width
 * @property null|float $return_height
 * @property null|float $return_weight
 * @property null|string $ref_number1
 * @property null|string $ref_number2
 * @property null|string $return_ref_number1
 * @property null|string $return_ref_number2
 * @property null|float $value
 * @property null|float $return_value
 */
class PendingParcel implements Arrayable, ArrayAccess, Jsonable, JsonSerializable
{
    /**
     * When set to true, there are multiple parcels (shipments) being created at once,
     * so we should treat fields as an array.
     */
    protected bool $multiMode = false;

    /**
     * When set to true, the `ShipmentOptions` object will add the print barcodes for
     * any reference numbers that are set.
     */
    protected bool $printBarcodes = false;

    protected WeightUom $weightUom;

    protected LengthUom $lengthUom;

    public function __construct(protected array $data = [], protected string $formFieldName = 'parcel')
    {
        if (! isset($this->data['uuid'])) {
            $this->data['uuid'] = (string) Str::uuid();
        }

        if (isset($this->data['print_barcodes']) && is_bool($this->data['print_barcodes'])) {
            $this->printBarcodes = $this->data['print_barcodes'];
        }

        $this->weightUom = $this->resolveWeightUom($data);
        $this->lengthUom = $this->resolveLengthUom($data);
    }

    public static function make(array $data = [], string $formFieldName = 'parcel'): self
    {
        return new static($data, $formFieldName);
    }

    public function usingFormFieldName(string $name): self
    {
        $this->formFieldName = $name;

        return $this;
    }

    public function usingMultiMode(): self
    {
        $this->multiMode = true;

        return $this;
    }

    public function usingWeightUom(WeightUom $uom): self
    {
        $this->weightUom = $uom;

        return $this;
    }

    public function usingLengthUom(LengthUom $uom): self
    {
        $this->lengthUom = $uom;

        return $this;
    }

    public function printBarcodes(): self
    {
        $this->printBarcodes = true;

        return $this;
    }

    public function livewirePropertyAttributes(string $field): HtmlString
    {
        return new HtmlString(implode(' ', [
            'x-data',
            'x-on:change="$wire.setParcelProperty(\'' . $this->uuid . '\', \'' . $field . '\', $el.value)"',
            'value="' . $this->{$field} . '"',
        ]));
    }

    public function formNameForProperty(string $field): string
    {
        if ($this->multiMode) {
            return "{$this->formFieldName}[{$this->uuid}][{$field}]";
        }

        return "{$this->formFieldName}.{$field}";
    }

    public function errorKey(): string
    {
        if ($this->multiMode) {
            return "{$this->formFieldName}.{$this->uuid}.*";
        }

        return "{$this->formFieldName}.*";
    }

    public function shouldPrintBarcodes(): bool
    {
        return $this->printBarcodes;
    }

    /**
     * If a key named `needs_return_label` is set to true, our CreateShipmentAction
     * will generate a return label for this parcel.
     */
    public function needsReturnLabel(): bool
    {
        return ($this->data['needs_return_label'] ?? false) === true;
    }

    public function dimensionalWeightForCarrier(CarrierEnum $carrier): float
    {
        return $this->calculateDimensionalWeight(
            carrier: $carrier,
            length: $this->length ?? 0,
            width: $this->width ?? 0,
            height: $this->height ?? 0,
        );
    }

    public function returnDimensionalWeightForCarrier(CarrierEnum $carrier): float
    {
        return $this->calculateDimensionalWeight(
            carrier: $carrier,
            length: $this->return_length ?? $this->length ?? 0,
            width: $this->return_width ?? $this->width ?? 0,
            height: $this->return_height ?? $this->height ?? 0,
        );
    }

    public function __get(string $name): mixed
    {
        return $this->offsetGet($name);
    }

    public function __set(string $name, $value): void
    {
        $this->offsetSet($name, $value);
    }

    public function __isset(string $name): bool
    {
        return $this->offsetExists($name);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * EasyPost requires that we send the weight in ounces and all dimensions in inches.
     */
    public function toShipmentArray(bool $isReturn = false): array
    {
        $length = $this->directionAwareDimensionValue('length', $isReturn);
        $width = $this->directionAwareDimensionValue('width', $isReturn);
        $height = $this->directionAwareDimensionValue('height', $isReturn);
        $weight = $this->directionAwareDimensionValue('weight', $isReturn);

        return [
            'length' => $this->lengthUom->toInches($length),
            'width' => $this->lengthUom->toInches($width),
            'height' => $this->lengthUom->toInches($height),
            'weight' => $this->weightUom->toOunces($weight),
        ];
    }

    public function toJson($options = 0)
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize(): mixed
    {
        return json_encode($this->data);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->data[$offset] ?? '';
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }

    protected function directionAwareDimensionValue(string $field, bool $isReturn = false): float
    {
        if (! $isReturn) {
            return (float) ($this->data[$field] ?? 0);
        }

        $value = $this->data["return_{$field}"] ?? $this->data[$field] ?? 0;

        return (float) $value;
    }

    protected function resolveWeightUom(array $initialData): WeightUom
    {
        if (isset($initialData['weight_uom'])) {
            $uom = WeightUom::tryFrom($initialData['weight_uom']);

            throw_unless(
                $uom instanceof WeightUom,
                InvalidUom::weight($initialData['weight_uom']),
            );

            return $uom;
        }

        return WeightUom::Ounce;
    }

    protected function resolveLengthUom(array $initialData): LengthUom
    {
        if (isset($initialData['length_uom'])) {
            $uom = LengthUom::tryFrom($initialData['length_uom']);

            throw_unless(
                $uom instanceof LengthUom,
                InvalidUom::length($initialData['length_uom']),
            );

            return $uom;
        }

        return LengthUom::Inch;
    }

    protected function calculateDimensionalWeight(
        CarrierEnum $carrier,
        mixed $length,
        mixed $width,
        mixed $height,
    ): float {
        return (new DimWeightCalculator)
            ->usingCarrierType($carrier)
            ->usingLength((float) $length)
            ->usingWidth((float) $width)
            ->usingHeight((float) $height)
            ->dimWeight();
    }
}
