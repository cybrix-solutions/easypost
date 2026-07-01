<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Enums\CarrierEnum;

it('provides a human readable label', function () {
    expect(CarrierEnum::Apc->label())->toBe('APC')
        ->and(CarrierEnum::AsendiaUsa->label())->toBe('Asendia USA');
});

it('provides a signup url for a carrier', function () {
    expect(CarrierEnum::Apc->signupUrl())->toBe('https://www.apc-pli.com/contact.html')
        ->and(CarrierEnum::AsendiaUsa->signupUrl())->toBe('https://www.asendiausa.com/contact/sales');
});

it('provides a signup text for a carrier that has a signup url', function () {
    expect(CarrierEnum::Apc->signupText())->toBe('Contact APC to create an account')
        ->and(CarrierEnum::AsendiaUsa->signupText())->toBe('Contact sales to create an account');
});

it('provides options for select fields on the add account process', function () {
    expect(CarrierEnum::AsendiaUsa->optionsFor('carrier_facility'))->toBe([
        'SFO' => 'SFO',
        'MIA' => 'MIA',
        'JFK' => 'JFK',
        'PHL' => 'PHL',
        'ORD' => 'ORD',
        'LAX' => 'LAX',
        'SLC' => 'SLC',
        'TOR' => 'TOR',
    ])
        ->and(CarrierEnum::AsendiaUsa->optionsFor('foo'))->toBe([]);
});

it('provides signup instructions for some carriers', function () {
    expect(CarrierEnum::Apc->signupInstructions())->toBeNull()
        ->and(CarrierEnum::AxleHireV3->signupInstructions())->toBe('You will need to contact AxelHire at 855-249-7447 to establish a shipping account.');
});

it('provides a signup help url for some carriers', function () {
    expect(CarrierEnum::Ups->signupHelpUrl())->toBe('https://support.easypost.com/hc/en-us/articles/360024355712-Setting-up-your-UPS-Account')
        ->and(CarrierEnum::Apc->signupHelpUrl())->toBeNull();
});

it('provides the field name of the name field for carriers', function () {
    expect(CarrierEnum::Ups->nameField())->toBe('name')
        ->and(CarrierEnum::Lso->nameField())->toBe('company');
});

it('provides the field name of the company field for carriers', function () {
    expect(CarrierEnum::Ups->companyField())->toBe('company')
        ->and(CarrierEnum::Lso->companyField())->toBe('name');
});

it('provides the number of days a carrier may void a label for', function () {
    config([
        'easypost.voidable_days' => [
            'default' => 2,
            'usps' => 1,
        ],
    ]);

    expect(CarrierEnum::Usps->voidableDays())->toBe(1)
        ->and(CarrierEnum::Ups->voidableDays())->toBe(2);
});

it('returns a boolean for if a carrier needs terms accepted for an account to be added', function (CarrierEnum $enum, bool $expectedResult) {
    expect($enum->needsTermsAccepted())->toBe($expectedResult);
})->with([
    [fn () => CarrierEnum::Ups, true],
    [fn () => CarrierEnum::Fedex, true],
    [fn () => CarrierEnum::Usps, false],
]);

it('excludes discontinued carriers from carrier search', function (CarrierEnum $enum, string $search) {
    expect($enum->isDisabled())->toBeTrue()
        ->and(CarrierEnum::fromSearch($search))->not->toContain($enum);
})->with([
    [fn () => CarrierEnum::FedexMailView, 'fedex mailview'],
    [fn () => CarrierEnum::Parcll, 'parcll'],
]);
