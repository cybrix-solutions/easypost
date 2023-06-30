<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\CarrierAccounts\ActivateCarrierAccountAction;
use CybrixSolutions\EasyPost\Events\CarrierAccountWasActivated;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Support\Facades\Event;

uses(UsesDatabase::class);

beforeEach(function () {
    config()->set('easypost.actions.activate_carrier_account', ActivateCarrierAccountAction::class);
});

it('activates a carrier account', function () {
    Event::fake();

    $account = CarrierAccount::factory()->inactive()->create();

    app(ActivateCarrierAccountAction::class)($account);

    expect($account->fresh()->isActive())->toBeTrue();

    Event::assertDispatched(function (CarrierAccountWasActivated $event) use ($account) {
        return $account->id === $event->account->id;
    });
});

it('does nothing if the account is already active', function () {
    Event::fake();

    $account = CarrierAccount::factory()->create();

    app(ActivateCarrierAccountAction::class)($account);

    Event::assertNotDispatched(CarrierAccountWasActivated::class);
});

it('makes the account default if no active defaulted accounts exist', function () {
    $account = CarrierAccount::factory()->inactive()->create();
    $otherAccount = CarrierAccount::factory()->inactive()->create(['default' => true]);

    expect($account->default)->toBeFalse();

    app(ActivateCarrierAccountAction::class)($account);

    expect($account->fresh()->default)->toBeTrue()
        ->and($otherAccount->fresh()->default)->toBeFalse();
});
