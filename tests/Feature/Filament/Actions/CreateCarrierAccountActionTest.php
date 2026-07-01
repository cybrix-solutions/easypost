<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Livewire\CarrierAccountManager;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierTypesMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\User;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

uses(UsesDatabase::class);

beforeEach(function () {
    config()->set('easypost.api_key', 'production_api_key');

    cache()->forget(config('easypost.cache.carriers.key'));

    mockProductionApi([
        CarrierTypesMock::make(),
    ]);

    actingAs(User::factory()->create());
});

it('opens the create carrier account modal', function () {
    livewire(CarrierAccountManager::class)
        ->mountAction('createCarrierAccount')
        ->assertMountedActionModalSee(__('easypost::livewire/carriers.accounts.actions.create.search.placeholder'));
});

it('opens the create carrier account modal from the empty state action', function () {
    livewire(CarrierAccountManager::class)
        ->assertTableActionVisible('createCarrierAccount')
        ->mountTableAction('createCarrierAccount')
        ->assertTableActionMounted('createCarrierAccount')
        ->assertMountedActionModalSee(__('easypost::livewire/carriers.accounts.actions.create.search.placeholder'));
});

it('opens the sync carrier accounts modal from the table header action', function () {
    livewire(CarrierAccountManager::class)
        ->assertTableHeaderActionsExistInOrder(['sync'])
        ->assertTableActionVisible('sync')
        ->mountAction('sync')
        ->assertActionMounted('sync')
        ->assertMountedActionModalSee(__('easypost::livewire/carriers.accounts.actions.sync.modal_submit'));
});

it('only shows carriers returned by easypost in the create modal', function () {
    livewire(CarrierAccountManager::class)
        ->mountAction('createCarrierAccount')
        ->assertMountedActionModalSee(CarrierEnum::Speedee->label())
        ->assertMountedActionModalDontSee(CarrierEnum::Apc->label())
        ->assertMountedActionModalDontSee(CarrierEnum::FedexMailView->label());
});

it('does not select a carrier type that was not returned by easypost', function () {
    livewire(CarrierAccountManager::class)
        ->call('selectCarrierType', CarrierEnum::Apc->value)
        ->assertSet('selectedCarrierType', null);
});
