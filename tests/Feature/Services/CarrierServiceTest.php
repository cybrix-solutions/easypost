<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Dto\EasyPostCredential;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Exceptions\InvalidCarrierForCustomWorkflow;
use CybrixSolutions\EasyPost\Services\CarrierService;
use CybrixSolutions\EasyPost\Tests\Fixtures\Responses\Carriers\CarrierResponses;
use EasyPost\EasyPostObject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

it('can be created from a carrier type', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(CarrierResponses::types());

    $service = CarrierService::fromType(CarrierEnum::Speedee);

    expect($service->carrierEnum())->toBe(CarrierEnum::Speedee)
        ->and(invade($service)->carrier)->toBeInstanceOf(EasyPostObject::class);
});

it('knows if the carrier type has test credentials', function () {
    Cache::shouldReceive('remember')
        ->twice()
        ->andReturn(CarrierResponses::types());

    $speedee = CarrierService::fromType(CarrierEnum::Speedee);
    $betterTrucks = CarrierService::fromType(CarrierEnum::BetterTrucks);

    expect($speedee->hasTestCredentials())->toBeFalse()
        ->and($betterTrucks->hasTestCredentials())->toBeTrue();
});

it('can get the signup url for a carrier', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(CarrierResponses::types());

    $service = CarrierService::fromType(CarrierEnum::Speedee);

    expect($service->signupUrl())->toBe(CarrierEnum::Speedee->signupUrl());
});

it('can get the signup text for a carrier', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(CarrierResponses::types());

    $service = CarrierService::fromType(CarrierEnum::Speedee);

    expect($service->signupText())->toBe(CarrierEnum::Speedee->signupText());
});

it('can get the signup help url for a carrier', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(CarrierResponses::types());

    $service = CarrierService::fromType(CarrierEnum::Ups);

    expect($service->signupText())->toBe(CarrierEnum::Ups->signupText());
});

it('can get the signup instructions for a carrier', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(CarrierResponses::types());

    $service = CarrierService::fromType(CarrierEnum::Speedee);

    expect($service->signupInstructions())->toBe(CarrierEnum::Speedee->signupInstructions());
});

it('can determine if the carrier has a custom creation workflow', function () {
    Cache::shouldReceive('remember')
        ->twice()
        ->andReturn(CarrierResponses::types());

    $speedee = CarrierService::fromType(CarrierEnum::Speedee);
    $ups = CarrierService::fromType(CarrierEnum::Ups);

    expect($speedee->isCustomWorkflow())->toBeFalse()
        ->and($ups->isCustomWorkflow())->toBeTrue();
});

it('can get the production credentials for adding a carrier account', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(CarrierResponses::types());

    $service = CarrierService::fromType(CarrierEnum::BetterTrucks);
    $credentials = $service->productionCredentials();

    expect($credentials)->toBeInstanceOf(Collection::class)
        ->and($credentials)->toHaveCount(1)
        ->and($credentials)->toContainOnlyInstancesOf(EasyPostCredential::class)
        ->and($credentials->first()->name())->toBe('api_key')
        ->and($credentials->first()->label())->toBe('Better Trucks API key');
});

it('can get the test env credentials for adding a carrier account', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(CarrierResponses::types());

    $service = CarrierService::fromType(CarrierEnum::BetterTrucks);
    $credentials = $service->testCredentials();

    expect($credentials)->toBeInstanceOf(Collection::class)
        ->and($credentials)->toHaveCount(1)
        ->and($credentials)->toContainOnlyInstancesOf(EasyPostCredential::class)
        ->and($credentials->first()->name())->toBe('api_key')
        ->and($credentials->first()->label())->toBe('Test Better Trucks API key');
});

it('can generate an array of validation rules for a carrier', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(CarrierResponses::types());

    $service = CarrierService::fromType(CarrierEnum::BetterTrucks);
    $rules = $service->rulesForValidation();

    $expectedRules = [
        'credentials.api_key' => [
            'required',
            'string',
        ],
        'test_credentials.api_key' => [
            'nullable',
            'string',
        ],
    ];

    expect($rules)->toBeArray()
        ->and($rules)->toMatchArray($expectedRules);
});

it('can generate an array of human-readable attributes for validation', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(CarrierResponses::types());

    $service = CarrierService::fromType(CarrierEnum::BetterTrucks);
    $attributes = $service->validationAttributes();

    $expectedAttributes = [
        'credentials.api_key' => 'Better Trucks API key',
        'test_credentials.api_key' => 'Test Better Trucks API key',
    ];

    expect($attributes)->toBeArray()
        ->and($attributes)->toMatchArray($expectedAttributes);
});

it('can get the credentials for a custom workflow', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(CarrierResponses::types());

    $service = CarrierService::fromType(CarrierEnum::Ups);
    $credentials = $service->customCredentials();

    // This is tested more thoroughly in the unit test for the custom workflows.
    expect($credentials)->toBeInstanceOf(Collection::class)
        ->and($credentials)->toHaveKeys(['account', 'company', 'address']);
});

it('will throw an exception when trying to retrieve custom credentials for non custom workflow carrier types', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(CarrierResponses::types());

    $service = CarrierService::fromType(CarrierEnum::Speedee);

    $service->customCredentials();
})->throws(InvalidCarrierForCustomWorkflow::class);

it('generates validation rules for custom workflow carrier types', function () {
    Cache::shouldReceive('remember')
        ->once()
        ->andReturn(CarrierResponses::types());

    $service = CarrierService::fromType(CarrierEnum::Ups);
    $rules = $service->rulesForValidation();

    // This is tested more thoroughly in the unit test for the custom workflows.
    expect($rules)->toHaveKeys([
        'accepted_terms',
        'registration_data.account_number',
    ]);
});
