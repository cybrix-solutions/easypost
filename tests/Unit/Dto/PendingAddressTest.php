<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Dto\PendingAddress;
use CybrixSolutions\EasyPost\Exceptions\Addresses\InvalidAddressProperty;

it('can be cast to array', function () {
    $pendingAddress = PendingAddress::make([
        'street1' => '123 Anystreet',
        'city' => 'Anytown',
        'state' => 'NY',
    ]);

    expect($pendingAddress->toArray())->toMatchArray([
        'street1' => '123 Anystreet',
        'city' => 'Anytown',
        'state' => 'NY',
        'residential' => false,
    ]);
});

test('convenience methods are offered to set the address properties', function () {
    $pendingAddress = PendingAddress::make()
        ->withStreet1('123 Anystreet')
        ->withCountry('US')
        ->withResidential(true);

    expect($pendingAddress->toArray())->toMatchArray([
        'street1' => '123 Anystreet',
        'country' => 'US',
        'residential' => true,
    ]);
});

it('throws an exception when an invalid property is given to the constructor', function () {
    PendingAddress::make(['foo' => 'bar']);
})->throws(InvalidAddressProperty::class);

it('can set properties on the internal data array', function () {
    $pendingAddress = PendingAddress::make();

    $pendingAddress->city = 'Anytown';

    expect($pendingAddress->city)->toBe('Anytown');
});

it('throws an exception when trying to set an invalid property', function () {
    $pendingAddress = PendingAddress::make();

    $pendingAddress->foo = 'bar';
})->throws(InvalidAddressProperty::class);

it('throws an exception when trying to access an invalid property like an object', function () {
    $pendingAddress = PendingAddress::make();

    $pendingAddress->foo;
})->throws(InvalidAddressProperty::class);

it('acts like an array', function () {
    $pendingAddress = PendingAddress::make();

    $pendingAddress['city'] = 'Anytown';

    expect($pendingAddress['city'])->toBe('Anytown');
});

it('throws an exception when trying to access an unknown property', function () {
    $pendingAddress = PendingAddress::make();

    $pendingAddress['foo'];
})->throws(InvalidAddressProperty::class);

it('can unset properties', function () {
    $pendingAddress = PendingAddress::make([
        'city' => 'Anytown',
    ]);

    expect($pendingAddress->city)->toBe('Anytown');

    unset($pendingAddress['city']);

    expect($pendingAddress->city)->toBeNull();
});
