<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Dto;

use CybrixSolutions\EasyPost\Casts\AddressCast;
use Exception;
use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\HtmlString;
use JsonSerializable;

/**
 * @property null|string $name
 * @property null|string $attention
 * @property null|string $street
 * @property null|string $city
 * @property null|string $state
 * @property null|string $country
 * @property null|string $postal_code
 * @property null|string $phone
 */
final class ShipmentAddress implements Castable, JsonSerializable, Jsonable
{
    protected array $attributes = [];

    public function __construct(null|string|array $data)
    {
        if (is_string($data)) {
            try {
                $this->attributes = json_decode($data, true);
            } catch (Exception) {
            }
        } elseif (is_array($data)) {
            $this->attributes = $data;
        }
    }

    public function asDisplay(): HtmlString
    {
        return new HtmlString(implode('<br>', array_filter([
            $this->attention,
            $this->phone,
            $this->name,
            $this->street,
            "{$this->city}, {$this->state} {$this->postal_code}",
        ])));
    }

    public static function castUsing(array $arguments)
    {
        return new AddressCast(...$arguments);
    }

    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __get(string $name): ?string
    {
        return $this->attributes[$name] ?? null;
    }

    public function __toString(): string
    {
        return $this->jsonSerialize();
    }

    public function toJson($options = 0)
    {
        return $this->jsonSerialize();
    }

    public function jsonSerialize(): mixed
    {
        return json_encode($this->attributes);
    }
}
