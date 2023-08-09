<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Dto;

use ArrayAccess;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Exceptions\Addresses\InvalidAddressProperty;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @property null|string $street1
 * @property null|string $street2
 * @property null|string $city
 * @property null|string $state
 * @property null|string $country
 * @property null|string $zip
 * @property null|string $company
 * @property null|string $name Attention name
 * @property null|string $phone
 * @property null|string $email
 * @property bool $residential
 */
final class PendingAddress implements Arrayable, ArrayAccess
{
    private static array $attributes = [
        'street1' => 'string',
        'street2' => 'string',
        'city' => 'string',
        'state' => 'string',
        'country' => 'string',
        'zip' => 'string',
        'company' => 'string',
        'name' => 'string',
        'phone' => 'string',
        'email' => 'string',
        'residential' => 'bool',
    ];

    private array $data = [];

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public static function make(array $data = []): self
    {
        return new self($data);
    }

    public function street(): string
    {
        return trim("{$this->street1} {$this->street2}");
    }

    public function withStreet1(?string $street1): self
    {
        $this->data['street1'] = $street1;

        return $this;
    }

    public function withStreet2(?string $street2): self
    {
        $this->data['street2'] = $street2;

        return $this;
    }

    public function withCity(?string $city): self
    {
        $this->data['city'] = $city;

        return $this;
    }

    public function withState(?string $state): self
    {
        $this->data['state'] = $state;

        return $this;
    }

    public function withCountry(?string $country): self
    {
        $this->data['country'] = $country;

        return $this;
    }

    public function withZip(?string $zip): self
    {
        $this->data['zip'] = $zip;

        return $this;
    }

    public function withCompany(?string $company): self
    {
        $this->data['company'] = $company;

        return $this;
    }

    public function withName(?string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function withPhone(?string $phone): self
    {
        $this->data['phone'] = $phone;

        return $this;
    }

    public function withEmail(?string $email): self
    {
        $this->data['email'] = $email;

        return $this;
    }

    public function withResidential(bool $residential): self
    {
        $this->data['residential'] = $residential;

        return $this;
    }

    public function __get(string $name): mixed
    {
        if (! array_key_exists($name, self::$attributes)) {
            throw InvalidAddressProperty::make($name);
        }

        $dataType = self::$attributes[$name];

        $fallbackValue = match ($dataType) {
            'string' => null,
            'bool' => false,
        };

        return $this->data[$name] ?? $fallbackValue;
    }

    public function __set(string $name, $value): void
    {
        if (! array_key_exists($name, self::$attributes)) {
            throw InvalidAddressProperty::make($name);
        }

        $this->data[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function toArray(): array
    {
        return array_merge($this->data, [
            'residential' => $this->residential,
        ]);
    }

    public function toShipmentArray(CarrierEnum $carrier, bool $isSender = false): array
    {
        $data = $this->toArray();

        $company = $data[$carrier->companyField()] ?? null;
        $name = $data[$carrier->nameField()] ?? null;

        if ($isSender) {
            unset($data['residential']);
        }

        return array_filter(array_merge($data, [
            'company' => $company,
            'name' => $name,
        ]));
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset};
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }
}
