<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

uses(UsesDatabase::class);

it('knows if it is active', function () {
    $account = CarrierAccount::factory()->make();

    expect($account->isActive())->toBeTrue();

    $account->deactivated_at = now();

    expect($account->isActive())->toBeFalse();
});

it('knows if it is an account provided by EasyPost', function () {
    $account = CarrierAccount::factory()->make();

    expect($account->isEasyPostAccount())->toBeFalse();

    $account->billing_type = 'easypost';

    expect($account->isEasyPostAccount())->toBeTrue();
});

it('can be scoped for active accounts only', function () {
    $active = CarrierAccount::factory()->count(2)->create();
    $inactive = CarrierAccount::factory()->inactive()->count(3)->create();

    $this->assertDatabaseCount(CarrierAccount::class, 5);

    $scoped = CarrierAccount::active()->get();

    expect($scoped->count())->toBe(2)
        ->and($scoped[0]->id)->toBe($active[0]->id)
        ->and($scoped[1]->id)->toBe($active[1]->id);
});

it('can find other inactive accounts', function () {
    $accounts = CarrierAccount::factory()->inactive()->count(3)->create();
    $activeAccount = CarrierAccount::factory()->create();

    $this->assertDatabaseCount(CarrierAccount::class, 4);

    $scoped = CarrierAccount::otherInactiveAccounts($accounts[0])->get();

    expect($scoped)->toHaveCount(2)
        ->and($scoped[0]->id)->not->toEqual($accounts[0]->id)
        ->and($scoped[1]->id)->not->toEqual($accounts[0]->id)
        ->and($scoped[0]->id)->not->toEqual($activeAccount->id)
        ->and($scoped[1]->id)->not->toEqual($activeAccount->id);
});

it('can find other accounts marked as "default"', function () {
    $nonDefault = CarrierAccount::factory()->create();
    $defaultAccount = CarrierAccount::factory()->isDefault()->create();

    $scoped = CarrierAccount::otherDefaultedAccounts($nonDefault)->get();

    expect($scoped)->toHaveCount(1)
        ->and($scoped[0]->id)->toEqual($defaultAccount->id);
});

it('can find other active accounts', function () {
    $accounts = CarrierAccount::factory()->count(3)->create();
    $inactiveAccount = CarrierAccount::factory()->inactive()->create();

    $this->assertDatabaseCount(CarrierAccount::class, 4);

    $scoped = CarrierAccount::otherActiveAccounts($accounts[0])->get();

    expect($scoped)->toHaveCount(2)
        ->and($scoped[0]->id)->not->toEqual($accounts[0]->id)
        ->and($scoped[1]->id)->not->toEqual($accounts[0]->id)
        ->and($scoped[0]->id)->not->toEqual($inactiveAccount->id)
        ->and($scoped[1]->id)->not->toEqual($inactiveAccount->id);
});

it('can be found by its EasyPost ID', function () {
    $account = CarrierAccount::factory()->create(['easypost_id' => 'my_id']);

    $queried = CarrierAccount::findByEasyPostId('my_id');

    expect($queried->id)->toEqual($account->id);
});

it('throws an exception when searching by EasyPost ID if the model does not exist', function () {
    CarrierAccount::findByEasyPostId('my_id');
})->throws(ModelNotFoundException::class);

it('if an account is created with default set to true, it will update the default status of all other accounts to false', function () {
    $account = CarrierAccount::factory()->isDefault()->create();

    expect($account->default)->toBeTrue();

    $otherAccount = CarrierAccount::factory()->isDefault()->create();

    expect($account->fresh()->default)->toBeFalse()
        ->and($otherAccount->fresh()->default)->toBeTrue();
});
