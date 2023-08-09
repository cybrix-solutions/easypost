<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\CarrierAccounts\MakeCarrierDefaultAction;
use CybrixSolutions\EasyPost\Events\CarrierAccounts\CarrierAccountWasMadeDefault;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\CustomCarrierAccount;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Support\Facades\Event;

uses(UsesDatabase::class);

beforeEach(function () {
    config()->set('easypost.actions.make_carrier_default', MakeCarrierDefaultAction::class);
});

it('makes a carrier account default', function () {
    Event::fake();

    $account = CarrierAccount::factory()->create();

    expect($account->default)->toBeFalse();

    app(MakeCarrierDefaultAction::class)($account);

    expect($account->fresh()->default)->toBeTrue();

    Event::assertDispatched(function (CarrierAccountWasMadeDefault $event) use ($account) {
        return $account->id === $event->account->id;
    });
});

it('does nothing if the account is already default', function () {
    Event::fake();

    $account = CarrierAccount::factory()->isDefault()->create();

    app(MakeCarrierDefaultAction::class)($account);

    Event::assertNotDispatched(CarrierAccountWasMadeDefault::class);
});

it('does nothing if the account is not active', function () {
    Event::fake();

    $account = CarrierAccount::factory()->inactive()->create();

    expect($account->default)->toBeFalse();

    app(MakeCarrierDefaultAction::class)($account);

    expect($account->fresh()->default)->toBeFalse();

    Event::assertNotDispatched(CarrierAccountWasMadeDefault::class);
});

it('removes the default flag from other accounts', function () {
    $account = CarrierAccount::factory()->create();
    $defaultAccount = CarrierAccount::factory()->isDefault()->create();

    expect($defaultAccount->default)->toBeTrue();

    app(MakeCarrierDefaultAction::class)($account);

    expect($account->fresh()->default)->toBeTrue()
        ->and($defaultAccount->fresh()->default)->toBeFalse();
});

it('respects the scoped scope when removing the default flag from other accounts', function () {
    config()->set('easypost.models.carrier_account', CustomCarrierAccount::class);

    $account = CustomCarrierAccount::factory()->create(['team_id' => 'my_team']);
    $defaultAccount = CustomCarrierAccount::factory()->isDefault()->create(['team_id' => 'my_team']);
    $otherTeamDefault = CustomCarrierAccount::factory()->isDefault()->create(['team_id' => 'other_team']);

    expect($defaultAccount->default)->toBeTrue()
        ->and($otherTeamDefault->default)->toBeTrue();

    Event::fake();

    app(MakeCarrierDefaultAction::class)($account);

    expect($account->fresh()->default)->toBeTrue()
        ->and($defaultAccount->fresh()->default)->toBeFalse()
        ->and($otherTeamDefault->fresh()->default)->toBeTrue();

    Event::assertDispatched(function (CarrierAccountWasMadeDefault $event) use ($account) {
        return $account->id === $event->account->id;
    });
});
