<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\CarrierAccounts\DeactivateCarrierAccountAction;
use CybrixSolutions\EasyPost\Events\CarrierAccounts\CarrierAccountWasDeactivated;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\CustomCarrierAccount;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;

uses(UsesDatabase::class);

beforeEach(function () {
    config()->set('easypost.actions.deactivate_carrier_account', DeactivateCarrierAccountAction::class);
});

it('deactivates a carrier account', function () {
    Event::fake();

    Date::setTestNow('2023-01-01');

    $account = CarrierAccount::factory()->create();

    app(DeactivateCarrierAccountAction::class)($account);

    expect($account->fresh()->isActive())->toBeFalse()
        ->and($account->fresh()->deactivated_at->format('Y-m-d'))->toEqual('2023-01-01');

    Event::assertDispatched(function (CarrierAccountWasDeactivated $event) use ($account) {
        return $account->id === $event->account->id;
    });
});

it('removes the default flag if there are other active accounts', function () {
    $account = CarrierAccount::factory()->isDefault()->create();
    [$other1, $other2] = CarrierAccount::factory()->count(2)->create();

    expect($other1->default)->toBeFalse()
        ->and($other2->default)->toBeFalse();

    app(DeactivateCarrierAccountAction::class)($account);

    expect($account->fresh()->default)->toBeFalse()
        ->and($other1->fresh()->default)->toBeTrue()
        ->and($other2->fresh()->default)->toBeFalse();
});

it('does not remove the default flag if there are no other active accounts', function () {
    $account = CarrierAccount::factory()->isDefault()->create();
    $other = CarrierAccount::factory()->inactive()->create();

    expect($other->default)->toBeFalse();

    app(DeactivateCarrierAccountAction::class)($account);

    expect($account->fresh()->default)->toBeTrue()
        ->and($other->fresh()->default)->toBeFalse();
});

it('does nothing if the account is already inactive', function () {
    Event::fake();

    Date::setTestNow('2023-01-01');

    $account = CarrierAccount::factory()->create([
        'deactivated_at' => '2022-01-01',
    ]);

    app(DeactivateCarrierAccountAction::class)($account);

    Event::assertNotDispatched(CarrierAccountWasDeactivated::class);

    expect($account->fresh()->deactivated_at->format('Y-m-d'))->toEqual('2022-01-01');
});

it('respects the scoped scope for custom carrier account models', function () {
    config([
        'easypost.models.carrier_account' => CustomCarrierAccount::class,
    ]);

    Event::fake();

    $account = CustomCarrierAccount::factory()->isDefault()->create([
        'team_id' => 'my_team',
    ]);
    $otherTeamAccount = CustomCarrierAccount::factory()->create([
        'team_id' => 'other_team',
    ]);
    $otherAccount = CustomCarrierAccount::factory()->create([
        'team_id' => 'my_team',
    ]);

    app(DeactivateCarrierAccountAction::class)($account);

    expect($account->fresh()->default)->toBeFalse()
        ->and($otherTeamAccount->fresh()->default)->toBeFalse()
        ->and($otherAccount->fresh()->default)->toBeTrue();
});
