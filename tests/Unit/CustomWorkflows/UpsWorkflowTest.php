<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\CustomWorkflows\UpsWorkflow;
use CybrixSolutions\EasyPost\Dto\EasyPostCredential;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Services\CarrierService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierTypesMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\Responses\Carriers\CarrierCredentials;
use EasyPost\EasyPostObject;
use Illuminate\Support\Collection;

beforeEach(function () {
    mockProductionApi([
        CarrierTypesMock::make(),
    ]);

    $this->workflow = new UpsWorkflow(CarrierService::fromType(CarrierEnum::Ups));
});

it('generates placeholders for the workflow fields', function () {
    expect($this->workflow->placeholders())->toMatchArray([
        'account_number' => '12A34B',
        'invoice_number' => '1234567',
        'invoice_date' => 'YYYYMMDD',
        'invoice_amount' => '100.00',
        'invoice_currency' => 'USD',
        'name' => 'John Doe',
        'email' => 'email@example.com',
        'phone' => '123-123-1234',
        'company' => 'Company Name',
        'website' => 'www.example.com',
        'title' => 'CTO',
        'street1' => '1234 Example St',
        'street2' => '2nd Fl',
        'city' => 'San Francisco',
        'state' => 'CA',
        'postal_code' => '94104',
        'country' => 'US',
    ]);
});

it('generates extra rules for specific fields', function (string $field, array $expectedRules) {
    expect($this->workflow->rulesForField($field))->toBe($expectedRules);
})->with([
    ['country', ['min:2', 'max:2']],
    ['state', []],
]);

it('determines if a certain field is required', function (string $field, EasyPostObject $credential, bool $isRequired) {
    expect($this->workflow->fieldIsRequired($field, $credential))->toBe($isRequired);
})->with([
    ['invoice_number', CarrierCredentials::textCredential(), false],
    ['invoice_date', CarrierCredentials::textCredential(), false],
    ['invoice_amount', CarrierCredentials::textCredential(), false],
    ['invoice_currency', CarrierCredentials::textCredential(), false],
    ['invoice_control_id', CarrierCredentials::textCredential(), false],
    ['client_ip', CarrierCredentials::textCredential(), false],
    ['street2', CarrierCredentials::textCredential(), false],
    ['account_number', CarrierCredentials::textCredential(), true],
]);

it('maps the credentials correctly', function () {
    $credentials = $this->workflow->credentials();

    expect($credentials)->toBeInstanceOf(Collection::class)
        ->and($credentials)->toHaveKeys(['account', 'company', 'address'])
        ->and($credentials['account'])->toContainOnlyInstancesOf(EasyPostCredential::class)
        ->and($credentials['account'])->toHaveKeys([
            'account_number',
            'invoice_number',
            'invoice_date',
            'invoice_amount',
            'invoice_currency',
            'invoice_control_id',
        ])
        ->and($credentials['company'])->toContainOnlyInstancesOf(EasyPostCredential::class)
        ->and($credentials['company'])->toHaveKeys([
            'name',
            'title',
            'company',
            'phone',
            'email',
            'website',
        ])
        ->and($credentials['address'])->toContainOnlyInstancesOf(EasyPostCredential::class)
        ->and($credentials['address'])->toHaveKeys([
            'street1',
            'street2',
            'city',
            'state',
            'postal_code',
            'country',
        ]);
});

it('generates the correct validation rules', function () {
    $rules = $this->workflow->validationRules();

    expect($rules)->toHaveKeys([
        'accepted_terms',
        'registration_data.account_number',
        'registration_data.invoice_number',
        'registration_data.street1',
        'registration_data.name',
    ])->and($rules['accepted_terms'])->toMatchArray(['accepted'])
        ->and($rules['registration_data.account_number'])->toMatchArray(['required', 'string'])
        ->and($rules['registration_data.invoice_date'])->toMatchArray(['nullable', 'string'])
        ->and($rules['registration_data.country'])->toMatchArray(['required', 'string', 'min:2', 'max:2']);
});
