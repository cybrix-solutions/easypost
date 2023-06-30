<?php

declare(strict_types=1);

// Note: These tests are based on the stub in resources/stubs/Actions/AddCarrierAccountAction.php.stub

use CybrixSolutions\EasyPost\Contracts\AddCarrierAccountAction as AddCarrierAccountActionContract;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Events\CarrierAccountWasCreated;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Services\CarrierService;
use CybrixSolutions\EasyPost\Tests\Fixtures\Actions\AddCarrierAccountAction;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierAccountMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierTypesMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\RequestData\CarrierAccountRequestData;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\post;

uses(UsesDatabase::class);

beforeEach(function () {
    config()->set('easypost.actions.add_carrier_account', AddCarrierAccountAction::class);

    Event::fake();

    mockProductionApi([
        CarrierTypesMock::make(),
        CarrierAccountMock::make()
            ->usingMethod('post')
            ->forAccountType(CarrierEnum::Speedee)
            ->forId('ca_123456'),
    ]);

    $this->carrierTypeService = CarrierService::fromType(CarrierEnum::Speedee);
    $this->action = app(AddCarrierAccountActionContract::class);
    $this->action->withCarrierService($this->carrierTypeService);
});

it('creates a carrier account in the api and stores it in the database', function () {
    $action = $this->action;

    $account = $action(
        CarrierAccountRequestData::make(CarrierEnum::Speedee)
            ->usingCarrierCredentials(CarrierAccountRequestData::speedeeCredentials())
            ->data()
    );

    expect($account->easypost_id)->toBe('ca_123456');

    $this->assertDatabaseHas('carrier_accounts', [
        'easypost_id' => 'ca_123456',
        'name' => 'Mocked Account',
        'type' => CarrierEnum::Speedee->value,
        'default' => true,
    ]);

    Event::assertDispatched(function (CarrierAccountWasCreated $event) use ($account) {
        return $event->carrierAccount->id === $account->id
            && $event->easyPostCarrierAccount->id === 'ca_123456';
    });
});

it('will not set an account as default if there already is a default, active account created', function () {
    CarrierAccount::factory()->isDefault()->create();

    $action = $this->action;

    $account = $action(
        CarrierAccountRequestData::make(CarrierEnum::Speedee)
            ->usingCarrierCredentials(CarrierAccountRequestData::speedeeCredentials())
            ->data()
    );

    $this->assertDatabaseHas('carrier_accounts', [
        'id' => $account->id,
        'easypost_id' => 'ca_123456',
        'name' => 'Mocked Account',
        'type' => CarrierEnum::Speedee->value,
        'default' => false,
    ]);

    $this->assertDatabaseCount('carrier_accounts', 2);
});

it('will set an account as default even if there is a defaulted account already created but it is inactive', function () {
    CarrierAccount::factory()->isDefault()->inactive()->create();

    $action = $this->action;

    $account = $action(
        CarrierAccountRequestData::make(CarrierEnum::Speedee)
            ->usingCarrierCredentials(CarrierAccountRequestData::speedeeCredentials())
            ->data()
    );

    $this->assertDatabaseHas('carrier_accounts', [
        'id' => $account->id,
        'easypost_id' => 'ca_123456',
        'name' => 'Mocked Account',
        'type' => CarrierEnum::Speedee->value,
        'default' => true,
    ]);

    $this->assertDatabaseCount('carrier_accounts', 2);
});

it('requires a name', function () {
    Route::post('/_test', function () {
        $action = $this->action;

        $action(
            CarrierAccountRequestData::make(CarrierEnum::Speedee)
                ->usingCarrierCredentials(CarrierAccountRequestData::speedeeCredentials())
                ->usingName('')
                ->data()
        );
    });

    post('/_test')
        ->assertSessionHasErrors([
            'name' => 'The name field is required.',
        ]);

    $this->assertDatabaseCount('carrier_accounts', 0);

    Event::assertNotDispatched(CarrierAccountWasCreated::class);
});

it('requires a unique name', function () {
    CarrierAccount::factory()->create(['name' => 'Mocked Account']);

    Route::post('/_test', function () {
        $action = $this->action;

        $action(
            CarrierAccountRequestData::make(CarrierEnum::Speedee)
                ->usingCarrierCredentials(CarrierAccountRequestData::speedeeCredentials())
                ->usingName('Mocked Account')
                ->data()
        );
    });

    post('/_test')
        ->assertSessionHasErrors([
            'name' => 'The name has already been taken.',
        ]);

    $this->assertDatabaseCount('carrier_accounts', 1);
});

it('validates the fields for a carrier account type', function (string $fieldToOmit) {
    Route::post('/_test', function () use ($fieldToOmit) {
        $action = $this->action;

        $action(
            CarrierAccountRequestData::make(CarrierEnum::Speedee)
                ->usingCarrierCredentials(
                    Arr::except(CarrierAccountRequestData::speedeeCredentials(), "credentials.{$fieldToOmit}")
                )
                ->data()
        );
    });

    post('/_test')
        ->assertSessionHasErrors("credentials.{$fieldToOmit}");

    $this->assertDatabaseCount('carrier_accounts', 0);
})->with([
    'account_number',
    'ftp_username',
    'ftp_password',
]);
