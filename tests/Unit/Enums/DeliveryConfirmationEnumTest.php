<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Enums\DeliveryConfirmationEnum;
use CybrixSolutions\EasyPost\Exceptions\Shipments\InvalidDeliveryConfirmation;

it('can be created from a value given by EasyPost', function (string $easypostValue, DeliveryConfirmationEnum $expectedCase) {
    $enum = DeliveryConfirmationEnum::fromEasyPostValue($easypostValue);

    expect($enum->value)->toBe($expectedCase->value);
})->with([
    ['NO_SIGNATURE', DeliveryConfirmationEnum::None],
    ['SIGNATURE', DeliveryConfirmationEnum::Signature],
    ['ADULT_SIGNATURE', DeliveryConfirmationEnum::Adult],
    ['INDIRECT_SIGNATURE', DeliveryConfirmationEnum::IndirectSignature],
    ['SIGNATURE_RESTRICTED', DeliveryConfirmationEnum::SignatureRestricted],
    ['ADULT_SIGNATURE_RESTRICTED', DeliveryConfirmationEnum::AdultSignatureRestricted],
]);

it('throws an exception for unsupported easypost delivery confirmation types', function () {
    DeliveryConfirmationEnum::fromEasyPostValue('fake-value');
})->throws(InvalidDeliveryConfirmation::class);

it('can get the available options for a given carrier type', function () {
    $options = DeliveryConfirmationEnum::forCarrier(CarrierEnum::Ups);

    expect($options)->toBeArray()
        ->toHaveCount(3)
        ->toMatchArray([
            DeliveryConfirmationEnum::None,
            DeliveryConfirmationEnum::Signature,
            DeliveryConfirmationEnum::Adult,
        ]);

    $fedexOptions = DeliveryConfirmationEnum::forCarrier(CarrierEnum::Fedex);
    expect($fedexOptions)
        ->toHaveCount(4)
        ->and(in_array(DeliveryConfirmationEnum::IndirectSignature, $fedexOptions))->toBeTrue();

    $uspsOptions = DeliveryConfirmationEnum::forCarrier(CarrierEnum::Usps);
    expect($uspsOptions)
        ->toHaveCount(5)
        ->and(in_array(DeliveryConfirmationEnum::SignatureRestricted, $uspsOptions))->toBeTrue()
        ->and(in_array(DeliveryConfirmationEnum::AdultSignatureRestricted, $uspsOptions))->toBeTrue();
});
