<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountRetrievalFailed;
use CybrixSolutions\EasyPost\Services\Api\ProductionEasyPostClient;
use CybrixSolutions\EasyPost\Services\CarrierAccountService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierAccountMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\DeleteAccountMock;
use EasyPost\CarrierAccount as EasyPostCarrierAccount;

beforeEach(function () {
    $this->service = app(CarrierAccountService::class);
});

it('can create a carrier account in the api', function () {
    // We are making an actual call to the API in this test.
    $account = makeAccount();

    expect($account->description)->toBe('**test account**');

    deleteAccount($account->id);
});

it('can find an existing account', function () {
    mockProductionApi([
        CarrierAccountMock::make()->forAccountType(CarrierEnum::Speedee)->forId('my_id'),
    ]);

    $account = $this->service->find('my_id');

    expect($account->id)->toBe('my_id')
        ->and($account->type)->toBe(CarrierEnum::Speedee->value)
        ->and(collect($account['fields']['credentials']))->toHaveCount(3);
});

it('throws an exception when trying to find an account that does not exist', function () {
    mockProductionApi([
        CarrierAccountMock::notFound(),
    ]);

    $this->service->find('fake-id');
})->throws(CarrierAccountRetrievalFailed::class, 'The requested resource could not be found.');

it('can delete an account', function () {
    mockProductionApi([
        CarrierAccountMock::make()->forId('my_id'),
        DeleteAccountMock::make()->forId('my_id'),
    ]);

    $deleted = $this->service->destroy('my_id');

    expect($deleted)->toBeTrue();
});

it('throws an exception when trying to delete an account that does not exist', function () {
    mockProductionApi([
        CarrierAccountMock::notFound()->forId('my_id'),
        DeleteAccountMock::make()->forId('my_id'),
    ]);

    $this->service->destroy('my_id');
})->throws(CarrierAccountRetrievalFailed::class, 'The requested resource could not be found.');

// Helpers
function makeAccount(string $type = null, array $data = null): EasyPostCarrierAccount
{
    // We're using Spee-Dee since their accounts can be created with fake data in the API.
    $type = $type ?? CarrierEnum::Speedee->value;
    $data = $data ?? [
        'credentials' => [
            'account_number' => 'test',
            'ftp_username' => 'test',
            'ftp_password' => 'test',
        ],
    ];

    return test()->service->create(
        type: $type,
        name: '**test account**',
        data: $data,
        reference: 'test-account--' . now()->timestamp,
    );
}

function deleteAccount(string $id): void
{
    app(ProductionEasyPostClient::class)->carrierAccount->delete($id);
}
