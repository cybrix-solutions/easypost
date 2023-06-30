<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\Livewire\TestCarrierAccountList;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\User;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

uses(UsesDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can activate a carrier account', function () {
    actingAs($this->user);

    $account = CarrierAccount::factory()->inactive()->create(['easypost_id' => 'my_id']);

    livewire(TestCarrierAccountList::class)
        ->call('activate', 'my_id')
        ->assertSuccessful();

    expect($account->fresh()->isActive())->toBeTrue();
});

it('authorizes a user for activating an account', function () {
    actingAs(User::factory()->notAllowed()->create());

    $account = CarrierAccount::factory()->inactive()->create(['easypost_id' => 'my_id']);

    livewire(TestCarrierAccountList::class)
        ->call('activate', 'my_id')
        ->assertForbidden();

    expect($account->fresh()->isActive())->toBeFalse();
});

it('throws an exception for an account not found when activating it', function () {
    actingAs($this->user);

    livewire(TestCarrierAccountList::class)
        ->call('activate', 'fake-id');
})->throws(ModelNotFoundException::class);

it('can deactivate a carrier account', function () {
    actingAs($this->user);

    $account = CarrierAccount::factory()->create(['easypost_id' => 'my_id']);

    livewire(TestCarrierAccountList::class)
        ->call('deactivate', 'my_id')
        ->assertSuccessful();

    expect($account->fresh()->isActive())->toBeFalse();
});

it('authorizes a user for deactivating an account', function () {
    actingAs(User::factory()->notAllowed()->create());

    $account = CarrierAccount::factory()->create(['easypost_id' => 'my_id']);

    livewire(TestCarrierAccountList::class)
        ->call('deactivate', 'my_id')
        ->assertForbidden();

    expect($account->fresh()->isActive())->toBeTrue();
});

it('throws an exception for an account not found when deactivating it', function () {
    actingAs($this->user);

    livewire(TestCarrierAccountList::class)
        ->call('deactivate', 'fake-id');
})->throws(ModelNotFoundException::class);

it('can make an account default', function () {
    actingAs($this->user);

    $account = CarrierAccount::factory()->create(['easypost_id' => 'my_id']);

    livewire(TestCarrierAccountList::class)
        ->call('makeDefault', 'my_id')
        ->assertSuccessful();

    expect($account->fresh()->default)->toBeTrue();
});

it('authorizes a user for marking an account as default', function () {
    actingAs(User::factory()->notAllowed()->create());

    $account = CarrierAccount::factory()->create(['easypost_id' => 'my_id']);

    livewire(TestCarrierAccountList::class)
        ->call('makeDefault', 'my_id')
        ->assertForbidden();

    expect($account->fresh()->default)->toBeFalse();
});

it('throws an exception for an account not found when marking as default', function () {
    actingAs($this->user);

    livewire(TestCarrierAccountList::class)
        ->call('makeDefault', 'fake-id');
})->throws(ModelNotFoundException::class);
