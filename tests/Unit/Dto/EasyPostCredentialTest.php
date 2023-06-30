<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\CustomWorkflows\UpsWorkflow;
use CybrixSolutions\EasyPost\Dto\EasyPostCredential;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Services\CarrierService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierTypesMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\Responses\Carriers\CarrierCredentials;

beforeEach(function () {
    mockProductionApi([
        CarrierTypesMock::make(),
    ]);
});

it('knows if it is a checkbox credential', function () {
    $credential = new EasyPostCredential(
        CarrierCredentials::checkboxCredential(),
        'my_credential',
        CarrierEnum::Speedee,
    );

    expect($credential->isCheckbox())->toBeTrue();

    $credential = new EasyPostCredential(
        CarrierCredentials::textCredential(),
        'my_credential',
        CarrierEnum::Speedee,
    );

    expect($credential->isCheckbox())->toBeFalse();
});

it('knows if it is a password credential', function () {
    $credential = new EasyPostCredential(
        CarrierCredentials::passwordCredential(),
        'my_credential',
        CarrierEnum::Speedee,
    );

    expect($credential->isPassword())->toBeTrue();

    $credential = new EasyPostCredential(
        CarrierCredentials::textCredential(),
        'my_credential',
        CarrierEnum::Speedee,
    );

    expect($credential->isPassword())->toBeFalse();
});

it('knows if it is a select field', function () {
    $credential = new EasyPostCredential(
        CarrierCredentials::selectCredential(),
        'my_credential',
        CarrierEnum::Speedee,
    );

    expect($credential->isSelect())->toBeTrue();

    $credential = new EasyPostCredential(
        CarrierCredentials::textCredential(),
        'my_credential',
        CarrierEnum::Speedee,
    );

    expect($credential->isSelect())->toBeFalse();
});

it('knows if it is required', function () {
    $credential = new EasyPostCredential(
        CarrierCredentials::textCredential(),
        'my_credential',
        CarrierEnum::Speedee,
    );

    expect($credential->isRequired())->toBeTrue();

    $credential = new EasyPostCredential(
        CarrierCredentials::optionalCredential(),
        'my_credential',
        CarrierEnum::Speedee,
    );

    expect($credential->isRequired())->toBeFalse();
});

test('test env credentials are always optional', function () {
    $credential = new EasyPostCredential(
        CarrierCredentials::textCredential(),
        'my_credential',
        CarrierEnum::Speedee,
        true,
    );

    expect($credential->isRequired())->toBeFalse();
});

it('returns the label for a credential', function () {
    $credential = new EasyPostCredential(
        CarrierCredentials::textCredential(),
        'my_credential',
        CarrierEnum::Speedee,
    );

    expect($credential->label())->toBe('Text Credential');
});

it('returns the name of a credential', function () {
    $credential = new EasyPostCredential(
        CarrierCredentials::textCredential(),
        'my_credential',
        CarrierEnum::Speedee,
    );

    expect($credential->name())->toBe('my_credential');
});

it('returns the options for a select credential', function () {
    $credential = new EasyPostCredential(
        CarrierCredentials::selectCredential(),
        'origin_hub',
        CarrierEnum::Parcll,
    );

    expect($credential->selectOptions())->toBe([
        'ES' => 'East',
        'WE' => 'West',
        'CE' => 'Central',
        'NE' => 'Northeast',
        'SE' => 'Southeast',
        'SO' => 'South',
    ]);
});

it('returns the rules for validation', function () {
    $credential = new EasyPostCredential(
        CarrierCredentials::textCredential(),
        'my_credential',
        CarrierEnum::Speedee,
    );

    expect($credential->rulesForValidation())->toBe([
        'required',
        'string',
    ]);

    $credential = new EasyPostCredential(
        CarrierCredentials::optionalCredential(),
        'my_credential',
        CarrierEnum::Speedee,
    );

    expect($credential->rulesForValidation())->toBe([
        'nullable',
        'string',
    ]);
});

it('returns the rules for validation on a custom workflow field', function () {
    $workflow = upsWorkflow();
    $credential = new EasyPostCredential(
        credential: CarrierCredentials::textCredential(),
        name: 'country',
        carrierEnum: CarrierEnum::Ups,
        workflow: $workflow,
    );

    expect($credential->rulesForValidation())->toMatchArray([
        'required',
        'string',
        'min:2',
        'max:2',
    ]);
});

it('can generate a placeholder for custom workflow fields', function () {
    $workflow = upsWorkflow();
    $credential = new EasyPostCredential(
        credential: CarrierCredentials::textCredential(),
        name: 'account_number',
        carrierEnum: CarrierEnum::Ups,
        workflow: $workflow,
    );

    expect($credential->placeholder())->toBe('12A34B');
});

it('can determine if certain custom workflow fields are optional', function () {
    $workflow = upsWorkflow();
    $credential = new EasyPostCredential(
        credential: CarrierCredentials::textCredential(),
        name: 'street2',
        carrierEnum: CarrierEnum::Ups,
        workflow: $workflow,
    );

    expect($credential->isRequired())->toBeFalse();
});

// Helpers

function upsWorkflow(): UpsWorkflow
{
    return new UpsWorkflow(CarrierService::fromType(CarrierEnum::Ups));
}
