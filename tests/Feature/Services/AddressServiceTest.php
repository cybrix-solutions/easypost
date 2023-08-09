<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Dto\AddressCandidate;
use CybrixSolutions\EasyPost\Dto\EasyPostAddress;
use CybrixSolutions\EasyPost\Dto\PendingAddress;
use CybrixSolutions\EasyPost\Exceptions\Addresses\AddressValidationFailed;
use CybrixSolutions\EasyPost\Services\AddressService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Addresses\AddressMock;
use EasyPost\Address;

beforeEach(function () {
    config()->set('easypost.test_mode', true);

    $this->api = app(AddressService::class);
});

it('creates an address', function () {
    mockApi([
        AddressMock::make(),
    ]);

    $address = $this->api->create(makePendingAddress());

    expect($address)
        ->toBeInstanceOf(EasyPostAddress::class)
        ->address->toBeInstanceOf(Address::class)
        ->address->verifications->toHaveCount(0);
});

it('creates and verifies an address', function () {
    mockApi([
        AddressMock::make()
            ->createAndVerify()
            ->withErrors([
                [
                    'code' => 'E.ADDRESS.NOT_FOUND',
                    'field' => 'address',
                    'message' => 'Address not found',
                ],
                [
                    'code' => 'E.HOUSE_NUMBER.MISSING',
                    'field' => 'street1',
                    'message' => 'House number is missing',
                ],
            ])
            ->forStreet1('Undeliverable St')
            ->forStreet2(null),
    ]);

    $address = $this->api->createAndVerify(makePendingAddress(['street1' => 'Undeliverable St', 'street2' => null]));

    expect($address)
        ->toBeInstanceOf(EasyPostAddress::class)
        ->isValid()->toBeFalse()
        ->street1->toBe('Undeliverable St')
        ->errors()->toHaveCount(2)
        ->errorMessage()->toBe('Address not found')
        ->errorMessage('street1')->toBe('House number is missing');
});

it('can suggest a corrected address', function () {
    mockApi([
        AddressMock::make()
            ->createAndVerify()
            ->forStreet1('417 MONTGOMERY ST FL 5')
            ->forStreet2(null)
            ->forZip('94104-1129'),
    ]);

    $address = $this->api->createAndVerify(makePendingAddress());

    expect($address)
        ->candidateShouldBeSuggested()->toBeTrue()
        ->and($address->addressCandidate())
        ->toBeInstanceOf(AddressCandidate::class)
        ->street()->toBe('417 MONTGOMERY ST FL 5')
        ->zip->toBe('94104-1129');
});

test('strict validation can be used', function () {
    mockApi([
        AddressMock::make()
            ->verifyStrict(),
    ]);

    try {
        $this->api->createAndVerify(
            makePendingAddress(['street1' => 'Undeliverable St', 'street2' => null]),
            strict: true,
        );
    } catch (AddressValidationFailed $e) {
        expect($e)
            ->getMessage()->toBe('Unable to verify address')
            ->errors->toHaveCount(2);
    }
});

it('can handle a bad request', function () {
    mockApi([
        AddressMock::make()
            ->notFound(),
    ]);

    $this->api->createAndVerify(makePendingAddress());
})->throws(AddressValidationFailed::class);

function makePendingAddress(array $overrides = []): PendingAddress
{
    $data = [
        'street1' => '417 Montgomery Street',
        'street2' => 'FLOOR 5',
        'city' => 'SAN FRANCISCO',
        'state' => 'CA',
        'country' => 'US',
        'zip' => '94104',
        'phone' => '405-123-4567',
        'company' => 'EasyPost',
    ];

    return PendingAddress::make(array_merge($data, $overrides));
}
