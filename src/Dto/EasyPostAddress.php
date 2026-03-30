<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Dto;

use EasyPost\Address;
use EasyPost\EasyPostObject;
use EasyPost\FieldError;
use EasyPost\Verifications;
use Illuminate\Support\Collection;

/**
 * @property string $id
 * @property string $street1
 * @property string $street2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $country
 * @property bool $residential
 * @property string $carrier_facility
 * @property string $name
 * @property string $company
 * @property string $phone
 * @property string $email
 * @property string $federal_tax_id
 * @property string $state_tax_id
 * @property Verifications $verifications
 */
final class EasyPostAddress
{
    private ?PendingAddress $pendingAddress = null;

    public function __construct(public Address $address) {}

    public function __get(string $name): mixed
    {
        return $this->address->{$name};
    }

    public function withPendingAddress(array|PendingAddress $pendingAddress): self
    {
        if (is_array($pendingAddress)) {
            $pendingAddress = PendingAddress::make($pendingAddress);
        }

        $this->pendingAddress = $pendingAddress;

        return $this;
    }

    public function street(): string
    {
        return trim("{$this->address->street1} {$this->address->street2}");
    }

    /**
     * We will suggest a correction to the end-user if one of the following is true:
     * 1. The street address does not match
     * 2. The city does not match
     * 3. The state does not match
     *
     * This method is only relevant during an address verification request. It will not work if you
     * are retrieving an address from the api.
     */
    public function candidateShouldBeSuggested(): bool
    {
        // If we don't have a pending address, we can't suggest a correction if we have
        // nothing to compare it to.
        if (! $this->pendingAddress) {
            return false;
        }

        if (strtolower($this->street()) !== strtolower($this->pendingAddress->street())) {
            return true;
        }

        if (strtolower($this->address->city) !== strtolower($this->pendingAddress->city)) {
            return true;
        }

        return strtolower($this->address->state) !== strtolower($this->pendingAddress->state);
    }

    /**
     * We will use the adjusted address returned from the API as a "candidate".
     */
    public function addressCandidate(): AddressCandidate
    {
        return AddressCandidate::fromEasyPostAddress($this->address);
    }

    public function errorMessage(?string $field = null): ?string
    {
        $errors = $this->errors();
        if ($errors->isEmpty()) {
            return null;
        }

        if ($field) {
            return $errors->filter(fn (EasyPostObject $error) => $error->field === $field)
                ->first()?->message;
        }

        return $errors->first()?->message;
    }

    /**
     * @return Collection<int, FieldError>
     */
    public function errors(): Collection
    {
        return collect($this->deliveryVerification()?->errors ?? []);
    }

    public function isValid(): bool
    {
        return $this->deliveryVerification()?->success === true;
    }

    public function deliveryVerification(): ?EasyPostObject
    {
        return $this->address->verifications['delivery'] ?? null;
    }
}
