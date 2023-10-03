<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\CarrierAccounts\DeleteCarrierAction;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierAccountMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\DeleteAccountMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\Livewire\TestCarrierAccountList;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\User;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

uses(UsesDatabase::class);

beforeEach(function () {
    config()->set('easypost.actions.delete_carrier_account', DeleteCarrierAction::class);

    mockProductionApi([
        CarrierAccountMock::make()
            ->forAccountType(CarrierEnum::Speedee)
            ->forId('ca_123456'),
        DeleteAccountMock::make()
            ->forId('ca_123456'),
    ]);

    $this->account = CarrierAccount::factory()->isDefault()->create(['easypost_id' => 'ca_123456']);
    $this->user = User::factory()->create();
});

it('confirms the deletion of an account', function () {
    actingAs($this->user);

    livewire(TestCarrierAccountList::class)
        ->call('confirmDelete', $this->account->easypost_id)
        ->assertSet('showDelete', true)
        ->assertSet('deleting.easypost_id', $this->account->easypost_id)
        ->assertEmitted('carrier_account.confirming-delete', $this->account->easypost_id);
});

it('deletes a carrier account', function () {
    actingAs($this->user);

    livewire(TestCarrierAccountList::class)
        ->call('confirmDelete', $this->account->easypost_id)
        ->call('deleteCarrier')
        ->assertSuccessful()
        ->assertSet('showDelete', false)
        ->assertSet('deleting', null)
        ->assertSet('deleteError', null)
        ->assertEmitted('carrier_account.deleted', $this->account->easypost_id);

    $this->assertDatabaseMissing(CarrierAccount::class, [
        'easypost_id' => 'ca_123456',
    ]);
});

it('does nothing if the deleting property is not set', function () {
    actingAs($this->user);

    livewire(TestCarrierAccountList::class)
        ->call('deleteCarrier')
        ->assertNotEmitted('carrier_account.deleted');
});

it('authorizes the deletion of a carrier account', function () {
    actingAs(User::factory()->notAllowed()->create());

    livewire(TestCarrierAccountList::class)
        ->call('confirmDelete', $this->account->easypost_id)
        ->call('deleteCarrier')
        ->assertForbidden();
});

it('sets an error message when the api request fails', function () {
    actingAs($this->user);

    mockProductionApi([
        CarrierAccountMock::notFound(),
    ]);

    $account = CarrierAccount::factory()->create(['easypost_id' => 'fake-id']);

    $component = livewire(TestCarrierAccountList::class)
        ->call('confirmDelete', $account->easypost_id)
        ->call('deleteCarrier')
        ->assertNotEmitted('carrier_account.deleted', $account->easypost_id)
        ->assertSet('deleting.easypost_id', $account->easypost_id)
        ->assertSet('showDelete', true);

    expect($component->deleteError)->toContain('The requested resource could not be found');
});

it('listens for confirmDelete from an external component', function () {
    actingAs($this->user);

    livewire(TestCarrierAccountList::class)
        ->emit('carrier_account.confirm-delete', 'ca_123456')
        ->assertSet('showDelete', true)
        ->assertEmitted('carrier_account.confirming-delete', 'ca_123456');
});

it('throws an exception when a carrier account is not found when confirming deletion', function () {
    actingAs($this->user);

    livewire(TestCarrierAccountList::class)
        ->call('confirmDelete', 'fake-id');
})->throws(ModelNotFoundException::class);
