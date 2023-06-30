<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Events\CarrierAccountWasCreated;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\Actions\AddCarrierAccountAction;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierAccountMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierTypesMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\Livewire\TestAddCarrierAccountForm;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\User;
use CybrixSolutions\EasyPost\Tests\Fixtures\RequestData\CarrierAccountRequestData;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Support\Facades\Event;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

uses(UsesDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);

    config()->set('easypost.actions.add_carrier_account', AddCarrierAccountAction::class);

    mockProductionApi([
        CarrierTypesMock::make(),
        CarrierAccountMock::make()
            ->usingMethod('post')
            ->forAccountType(CarrierEnum::Speedee)
            ->forId('ca_123456'),
    ]);
});

it('has a method to show a form to search for carrier account types and add a carrier account', function () {
    livewire(TestAddCarrierAccountForm::class)
        ->assertSeeText('Button to show the form')
        ->call('add')
        ->assertSuccessful()
        ->assertDontSeeText('Button to show the form')
        ->assertSee('add-carrier-form')
        ->assertSee('carrier-search-form')
        ->assertSet('show', true)
        ->assertSet('carrierSearch', '')
        ->assertSet('carrierType', '')
        ->assertSet('state.name', '')
        ->assertSet('errorMessage', null);
});

it('listens for an event emitted from an external component to show the form', function () {
    livewire(TestAddCarrierAccountForm::class)
        ->emit('add-carrier')
        ->assertSet('show', true);
});

it('does not show the form if a production api key is not set', function () {
    config()->set('easypost.api_key', null);

    livewire(TestAddCarrierAccountForm::class)
        ->call('add')
        ->assertSet('show', false)
        ->assertDontSee('add-carrier-form');
});

it('can select a carrier type', function () {
    livewire(TestAddCarrierAccountForm::class)
        ->call('add')
        ->call('selectCarrier', CarrierEnum::Speedee->value)
        ->assertSet('carrierType', CarrierEnum::Speedee->value)
        ->assertSet('errorMessage', null)
        ->assertSet('carrierEnum', CarrierEnum::Speedee)
        ->assertDontSee('carrier-search-form')
        ->assertSee('carrier-account-fields');
});

it('provides an error message when an invalid carrier type is selected', function () {
    livewire(TestAddCarrierAccountForm::class)
        ->call('add')
        ->call('selectCarrier', 'invalid')
        ->assertSet('carrierType', '')
        ->assertSet('errorMessage', __('easypost::validation.invalid_carrier_chosen'));
});

it('can filter the available carriers from a search term', function () {
    livewire(TestAddCarrierAccountForm::class)
        ->call('add')
        ->assertSeeText(CarrierEnum::Speedee->label())
        ->assertSeeText(CarrierEnum::Fedex->label())
        ->set('carrierSearch', CarrierEnum::Fedex->label())
        ->assertDontSeeText(CarrierEnum::Speedee->label())
        ->assertSeeText(CarrierEnum::Fedex->label());
});

it('shows the fields required to add a given carrier account', function () {
    livewire(TestAddCarrierAccountForm::class)
        ->call('add')
        ->call('selectCarrier', CarrierEnum::Speedee->value)
        ->assertSee('account_number')
        ->assertSee('ftp_username')
        ->assertSee('ftp_password');
});

it('provides a way to remove the selected carrier and go back to the carrier search form', function () {
    livewire(TestAddCarrierAccountForm::class)
        ->call('add')
        ->call('selectCarrier', CarrierEnum::Speedee->value)
        ->assertDontSee('carrier-search-form')
        ->assertSee('carrier-account-fields')
        ->call('back')
        ->assertSee('carrier-search-form')
        ->assertDontSee('carrier-account-fields');
});

// Note: The tests for storing are under the assumption that the trait is using the stubbed out AddCarrierAccountAction class.

it('can add a new carrier account to the api and database', function () {
    Event::fake();

    livewire(TestAddCarrierAccountForm::class)
        ->call('add')
        ->call('selectCarrier', CarrierEnum::Speedee->value)
        ->set(
            'state',
            CarrierAccountRequestData::make()
                ->usingCarrierCredentials(CarrierAccountRequestData::speedeeCredentials())
                ->data()
        )
        ->call('store')
        ->assertSuccessful()
        ->assertEmitted('carrier_account.added')
        ->assertSet('show', false)
        ->assertSet('errorMessage', null)
        ->assertSet('carrierType', '')
        ->assertSet('carrierSearch', '');

    $this->assertDatabaseHas(CarrierAccount::class, [
        'name' => 'Mocked Account',
        'easypost_id' => 'ca_123456',
    ]);

    Event::assertDispatched(CarrierAccountWasCreated::class);
});

it('does not create an account if validation fails', function () {
    Event::fake();

    livewire(TestAddCarrierAccountForm::class)
        ->call('add')
        ->call('selectCarrier', CarrierEnum::Speedee->value)
        ->set(
            'state',
            CarrierAccountRequestData::make()
                ->usingCarrierCredentials(CarrierAccountRequestData::speedeeCredentials())
                ->usingName('')
                ->data()
        )
        ->call('store')
        ->assertHasErrors(['name' => 'required']);

    $this->assertDatabaseCount(CarrierAccount::class, 0);

    Event::assertNotDispatched(CarrierAccountWasCreated::class);
});

it('authorizes for creating a carrier account', function () {
    Event::fake();

    actingAs(User::factory()->notAllowed()->create());

    livewire(TestAddCarrierAccountForm::class)
        ->call('add')
        ->call('selectCarrier', CarrierEnum::Speedee->value)
        ->set(
            'state',
            CarrierAccountRequestData::make()
                ->usingCarrierCredentials(CarrierAccountRequestData::speedeeCredentials())
                ->data()
        )
        ->call('store')
        ->assertForbidden();

    Event::assertNotDispatched(CarrierAccountWasCreated::class);
});
