<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services;

use CybrixSolutions\EasyPost\Dto\EasyPostAddress;
use CybrixSolutions\EasyPost\Dto\PendingAddress;
use CybrixSolutions\EasyPost\Exceptions\Addresses\AddressCreationFailed;
use CybrixSolutions\EasyPost\Exceptions\Addresses\AddressValidationFailed;
use CybrixSolutions\EasyPost\Services\Api\EasyPostClient;
use EasyPost\Exception\Api\ApiException;
use EasyPost\Exception\Api\InvalidRequestException;

final readonly class AddressService
{
    public function __construct(private EasyPostClient $api)
    {
    }

    public function create(array|PendingAddress $address): EasyPostAddress
    {
        $addressData = $address instanceof PendingAddress ? $address->toArray() : $address;

        try {
            $apiAddress = $this->api->address->create($addressData);

            return (new EasyPostAddress($apiAddress))->withPendingAddress($address);
        } catch (ApiException $e) {
            throw AddressCreationFailed::because($e->getMessage());
        }
    }

    public function createAndVerify(array|PendingAddress $address, bool $strict = false): EasyPostAddress
    {
        $addressData = $address instanceof PendingAddress ? $address->toArray() : $address;

        $key = $strict ? 'verify_strict' : 'verify';
        $addressData[$key] = true;

        try {
            $apiAddress = $this->api->address->create($addressData);

            return (new EasyPostAddress($apiAddress))->withPendingAddress($address);
        } catch (InvalidRequestException $e) {
            throw AddressValidationFailed::fromInvalidRequest($e);
        } catch (ApiException $e) {
            throw AddressValidationFailed::because($e->getMessage());
        }
    }
}
