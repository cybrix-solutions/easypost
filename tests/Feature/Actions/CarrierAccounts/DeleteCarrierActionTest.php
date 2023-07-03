<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\CarrierAccounts\DeleteCarrierAction;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Events\CarrierAccountWasDeleted;
use CybrixSolutions\EasyPost\Exceptions\CarrierAccounts\CarrierAccountDeletionFailed;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierAccountMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\DeleteAccountMock;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Support\Facades\Event;

uses(UsesDatabase::class);

beforeEach(function () {
    config()->set('easypost.actions.delete_carrier_account', DeleteCarrierAction::class);

    Event::fake();

    mockProductionApi([
        CarrierAccountMock::make()
            ->forAccountType(CarrierEnum::Speedee)
            ->forId('ca_123456'),
        DeleteAccountMock::make()
            ->forId('ca_123456'),
    ]);

    $this->account = CarrierAccount::factory()->isDefault()->create(['easypost_id' => 'ca_123456']);
});

it('deletes a carrier account in the api and database', function () {
    app(DeleteCarrierAction::class)($this->account);

    $this->assertDatabaseMissing(CarrierAccount::class, [
        'easypost_id' => 'ca_123456',
    ]);

    Event::assertDispatched(function (CarrierAccountWasDeleted $event) {
        return $event->account->id === $this->account->id;
    });
});

it('sets another active account to default if the account being deleted is marked as default', function () {
    $otherAccount = CarrierAccount::factory()->create();

    expect($otherAccount->default)->toBeFalse();

    app(DeleteCarrierAction::class)($this->account);

    expect($otherAccount->fresh()->default)->toBeTrue();
});

it('throws an exception for an account that does not exist on EasyPost', function () {
    mockProductionApi([
        CarrierAccountMock::notFound(),
    ]);

    $otherAccount = CarrierAccount::factory()->create(['easypost_id' => 'fake-id']);

    app(DeleteCarrierAction::class)($otherAccount);
})->throws(CarrierAccountDeletionFailed::class);

it('does not dispatch the deleted event if the api call fails', function () {
    mockProductionApi([
        CarrierAccountMock::notFound(),
    ]);

    $otherAccount = CarrierAccount::factory()->create(['easypost_id' => 'fake-id']);

    try {
        app(DeleteCarrierAction::class)($otherAccount);
    } catch (CarrierAccountDeletionFailed) {
    }

    Event::assertNotDispatched(CarrierAccountWasDeleted::class);
});
