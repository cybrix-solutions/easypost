<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Casts;

use CybrixSolutions\EasyPost\Dto\ShipmentAddress;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AddressCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return new ShipmentAddress($value);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if (is_array($value) || is_null($value)) {
            $value = new ShipmentAddress($value);
        }

        return (string) $value;
    }

    public function serialize(Model $model, string $key, mixed $value, array $attributes): ?array
    {
        return $value?->toArray();
    }
}
