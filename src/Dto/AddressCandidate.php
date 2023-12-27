<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Dto;

use EasyPost\Address;

final readonly class AddressCandidate
{
    public ?string $street1;

    public ?string $street2;

    public ?string $city;

    public ?string $state;

    public ?string $country;

    public ?string $zip;

    public function __construct(Address $address)
    {
        $this->street1 = $address->street1;
        $this->street2 = $address->street2;
        $this->city = $address->city;
        $this->state = $address->state;
        $this->country = $address->country;
        $this->zip = $address->zip;
    }

    public static function fromEasyPostAddress(Address $address): self
    {
        return new self($address);
    }

    public function street(): string
    {
        return trim("{$this->street1} {$this->street2}");
    }
}
