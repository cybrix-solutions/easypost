<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\CarrierAccounts\UpdateCarrierAction;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Events\CarrierAccounts\CarrierAccountWasUpdated;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierAccountMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierTypesMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\Livewire\TestEditCarrierAccountForm;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\User;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Event;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

uses(UsesDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);

    config()->set('easypost.actions.update_carrier_account', UpdateCarrierAction::class);

    mockProductionApi([
        CarrierTypesMock::make(),
        CarrierAccountMock::make()->forId('ca_123456')->forAccountType(CarrierEnum::Speedee),
        CarrierAccountMock::make()
            ->usingMethod('patch')
            ->forId('ca_123456'),
    ]);

    $this->account = CarrierAccount::factory()->create([
        'type' => CarrierEnum::Speedee->value,
        'easypost_id' => 'ca_123456',
        'name' => 'My Account',
    ]);
});

it('listens for an event to show the edit form', function () {
    livewire(TestEditCarrierAccountForm::class)
        ->assertSeeText('Button to show form')
        ->emit('carrier_account.edit', 'ca_123456')
        ->assertSee('edit-carrier-form')
        ->assertSet('show', true)
        ->assertSet('errorMessage', null)
        ->assertSet('editingId', 'ca_123456')
        ->assertSet('carrierType', CarrierEnum::Speedee->value)
        ->assertSet('state.name', 'My Account')
        ->assertSet('state.credentials.account_number', 'test')
        ->assertSet('state.credentials.ftp_username', 'test')
        ->assertSet('state.credentials.ftp_password', '*******')
        ->assertSeeText('Spee-Dee Account Number');
});

it('authorizes a user to see the edit form', function () {
    actingAs(User::factory()->notAllowed()->create());

    livewire(TestEditCarrierAccountForm::class)
        ->emit('carrier_account.edit', 'ca_123456')
        ->assertForbidden();
});

it('throws an exception for an account not found', function () {
    livewire(TestEditCarrierAccountForm::class)
        ->emit('carrier_account.edit', 'fake-id');
})->throws(ModelNotFoundException::class);

it('updates the name of a carrier account', function () {
    Event::fake();

    livewire(TestEditCarrierAccountForm::class)
        ->emit('carrier_account.edit', 'ca_123456')
        ->set('state.name', 'New Name')
        ->call('update')
        ->assertSuccessful()
        ->assertEmitted('carrier_account.updated', 'ca_123456');

    expect($this->account->fresh()->name)->toBe('New Name');

    Event::assertDispatched(function (CarrierAccountWasUpdated $event) {
        return $event->account->id === $this->account->id;
    });
});

it('shows an error message if the api call fails', function () {
    Event::fake();

    $component = livewire(TestEditCarrierAccountForm::class)
        ->emit('carrier_account.edit', 'ca_123456');

    mockProductionApi([
        CarrierAccountMock::notFound()->forId('ca_123456'),
    ]);

    $component->call('update')
        ->assertSet('errorMessage', 'We were unable to update your carrier account at this time - message from EasyPost: The requested resource could not be found.');

    Event::assertNotDispatched(CarrierAccountWasUpdated::class);
});

it('does not edit the account if validation fails', function () {
    Event::fake();

    livewire(TestEditCarrierAccountForm::class)
        ->emit('carrier_account.edit', 'ca_123456')
        ->set('state.name', '')
        ->call('update')
        ->assertHasErrors(['name' => 'required']);

    expect($this->account->fresh()->name)->toBe('My Account');

    Event::assertNotDispatched(CarrierAccountWasUpdated::class);
});

it('authorizes the user to update the account', function () {
    Event::fake();

    $component = livewire(TestEditCarrierAccountForm::class)
        ->emit('carrier_account.edit', 'ca_123456')
        ->set('state.name', 'New Name');

    actingAs(User::factory()->notAllowed()->create());

    $component->call('update')
        ->assertForbidden();

    Event::assertNotDispatched(CarrierAccountWasUpdated::class);

    expect($this->account->fresh()->name)->toBe('My Account');
});

it('does nothing if there is not an editingId property set', function () {
    Event::fake();

    livewire(TestEditCarrierAccountForm::class)
        ->call('update')
        ->assertNotEmitted('carrier_account.updated');

    Event::assertNotDispatched(CarrierAccountWasUpdated::class);
});
