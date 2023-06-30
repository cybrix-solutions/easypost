<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\CustomWorkflows\FedexWorkflow;
use CybrixSolutions\EasyPost\Dto\EasyPostCredential;
use CybrixSolutions\EasyPost\Services\CarrierService;
use CybrixSolutions\EasyPost\Tests\Fixtures\Responses\Carriers\CarrierResponses;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->workflow = new FedexWorkflow(new CarrierService(CarrierResponses::fedexAccount()));
});

it('returns placeholders for the workflow fields', function () {
    expect($this->workflow->placeholders())->toBe([
        'corporate_first_name' => 'John',
        'corporate_last_name' => 'Doe',
        'corporate_job_title' => 'Manager',
        'corporate_company_name' => 'Company Name',
        'corporate_phone_number' => '123-123-1234',
        'corporate_email_address' => 'email@example.com',
        'corporate_streets' => '1234 Example St. Suite 123',
        'corporate_city' => 'San Francisco',
        'corporate_state' => 'CA',
        'corporate_postal_code' => '94104',
        'corporate_country_code' => 'US',
        'shipping_streets' => '1234 Example St. Suite 123',
        'shipping_city' => 'San Francisco',
        'shipping_state' => 'CA',
        'shipping_postal_code' => '94104',
        'shipping_country_code' => 'US',
    ]);
});

it('returns custom rules for certain fields', function (string $field, array $expectedRules) {
    expect($this->workflow->rulesForField($field))->toBe($expectedRules);
})->with([
    ['corporate_country_code', ['min:2', 'max:2']],
    ['shipping_country_code', ['min:2', 'max:2']],
    ['shipping_state', []],
]);

it('maps the credentials correctly', function () {
    $credentials = $this->workflow->credentials();

    expect($credentials)->toBeInstanceOf(Collection::class)
        ->and($credentials)->toHaveKeys(['credential_information', 'company_information', 'address_information'])
        ->and($credentials['credential_information'])->toContainOnlyInstancesOf(EasyPostCredential::class)
        ->and($credentials['credential_information'])->toHaveKeys(['account_number'])
        ->and($credentials['company_information'])->toContainOnlyInstancesOf(EasyPostCredential::class)
        ->and($credentials['company_information'])->toHaveKeys([
            'corporate_first_name',
            'corporate_last_name',
            'corporate_job_title',
            'corporate_company_name',
            'corporate_phone_number',
            'corporate_email_address',
            'corporate_streets',
            'corporate_city',
            'corporate_state',
            'corporate_postal_code',
            'corporate_country_code',
        ])
        ->and($credentials['address_information'])->toContainOnlyInstancesOf(EasyPostCredential::class)
        ->and($credentials['address_information'])->toHaveKeys([
            'shipping_streets',
            'shipping_city',
            'shipping_state',
            'shipping_postal_code',
            'shipping_country',
        ]);
});

it('generates the correct validation rules', function () {
    $rules = $this->workflow->validationRules();

    expect($rules)->toHaveKeys([
        'accepted_terms',
        'registration_data.account_number',
        'registration_data.corporate_first_name',
        'registration_data.shipping_streets',
    ])->and($rules['accepted_terms'])->toMatchArray(['accepted'])
        ->and($rules['registration_data.shipping_streets'])->toMatchArray(['required', 'string'])
        ->and($rules['registration_data.shipping_country'])->toMatchArray(['required', 'string', 'min:2', 'max:2']);
});
