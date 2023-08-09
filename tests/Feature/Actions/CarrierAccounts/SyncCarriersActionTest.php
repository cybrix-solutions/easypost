<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\CarrierAccounts\SyncCarriersAction;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\SyncCarriersAction as SyncCarriersActionContract;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Events\CarrierAccounts\CarrierAccountWasCreated;
use CybrixSolutions\EasyPost\Events\CarrierAccounts\CarrierAccountWasUpdated;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountSyncFailed;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierAccountsListMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\CustomCarrierAccount;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use EasyPost\CarrierAccount as EasyPostCarrierAccount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

uses(UsesDatabase::class);

beforeEach(function () {
    config()->set('easypost.actions.sync_carriers', SyncCarriersAction::class);
    config()->set('easypost.cache.carrier_account.key', 'easypost::carriers.{account}');

    mockProductionApi([
        CarrierAccountsListMock::make(),
    ]);

    $this->account = CarrierAccount::factory()->create([
        'type' => CarrierEnum::Speedee->value,
        'easypost_id' => 'ca_speedee',
        'name' => 'Name that will be changed',
    ]);

    $this->action = app(SyncCarriersActionContract::class);
});

it('syncs all carrier accounts', function () {
    Cache::spy();
    Event::fake();

    $action = $this->action;

    $action();

    $this->assertDatabaseCount(CarrierAccount::class, 2);

    $this->assertDatabaseHas(CarrierAccount::class, [
        'id' => $this->account->id,
        'easypost_id' => 'ca_speedee',
        'name' => 'Spee-Dee Mocked Account',
    ]);

    $this->assertDatabaseHas(CarrierAccount::class, [
        'easypost_id' => 'ca_ups',
        'name' => 'UPS Mocked Account',
    ]);

    Cache::shouldHaveReceived('forget')->with('easypost::carriers.ca_speedee')->once();
    Cache::shouldHaveReceived('forget')->with('easypost::carriers.ca_ups')->once();

    Event::assertDispatched(function (CarrierAccountWasUpdated $event) {
        return $event->account->id === $this->account->id;
    });

    Event::assertDispatched(function (CarrierAccountWasCreated $event) {
        return $event->carrierAccount->easypost_id === 'ca_ups'
            && $event->easyPostCarrierAccount->id === 'ca_ups';
    });
});

test('custom context can be added for syncing accounts', function () {
    $this->account->delete();
    config()->set('easypost.models.carrier_account', CustomCarrierAccount::class);

    $action = $this->action;

    $action->withContext([
        'team_id' => 'my_team',
    ]);

    $action();

    $this->assertDatabaseCount(CustomCarrierAccount::class, 2);

    $this->assertDatabaseHas(CustomCarrierAccount::class, [
        'easypost_id' => 'ca_speedee',
        'team_id' => 'my_team',
    ]);

    $this->assertDatabaseHas(CustomCarrierAccount::class, [
        'easypost_id' => 'ca_ups',
        'team_id' => 'my_team',
    ]);
});

test('custom filtering can be applied to the list of accounts that will be synced', function () {
    $this->account->delete();
    config()->set('easypost.models.carrier_account', CustomCarrierAccount::class);

    $action = $this->action;

    $action->withContext([
        'team_id' => 'my_team',
    ])->withAccountFilter(function (EasyPostCarrierAccount $carrierAccount) {
        return $carrierAccount->id === 'ca_ups';
    });

    $action();

    $this->assertDatabaseCount(CustomCarrierAccount::class, 1);
    $this->assertDatabaseHas(CustomCarrierAccount::class, [
        'easypost_id' => 'ca_ups',
        'team_id' => 'my_team',
    ]);
});

it('throws an exception when an api error occurs', function () {
    mockProductionApi([
        CarrierAccountsListMock::badRequest(),
    ]);

    $action = $this->action;

    $action();
})->throws(
    CarrierAccountSyncFailed::class,
    'We were unable to sync your carrier accounts at this time - message from EasyPost: Malformed request. Please check the contents and retry.'
);
