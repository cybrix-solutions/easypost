<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\CarrierAccounts\UpdateCarrierAction;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\UpdateCarrierAction as UpdateCarrierActionContract;
use CybrixSolutions\EasyPost\Enums\CarrierEnum;
use CybrixSolutions\EasyPost\Events\CarrierAccounts\CarrierAccountWasUpdated;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Services\CarrierService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierAccountMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierTypesMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\CustomCarrierAccount;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\post;

uses(UsesDatabase::class);

beforeEach(function () {
    config()->set('easypost.actions.update_carrier_account', UpdateCarrierAction::class);
    config()->set('easypost.cache.carrier_account.key', 'easypost::carriers.{account}');

    Event::fake();

    mockProductionApi([
        CarrierTypesMock::make(),
        CarrierAccountMock::make()
            ->forAccountType(CarrierEnum::Speedee)
            ->forId('ca_123456'),
        CarrierAccountMock::make()
            ->usingMethod('patch')
            ->forAccountType(CarrierEnum::Speedee)
            ->forId('ca_123456'),
    ]);

    $this->account = CarrierAccount::factory()->create([
        'name' => 'My Account',
        'easypost_id' => 'ca_123456',
        'type' => CarrierEnum::Speedee->value,
    ]);

    $this->carrierService = CarrierService::fromAccount('ca_123456');

    $this->action = app(UpdateCarrierActionContract::class);
    $this->action
        ->withCarrierService($this->carrierService)
        ->withStoredValues($this->carrierService->storedValues());
});

it('updates a carrier account', function () {
    $action = $this->action;

    Cache::spy();

    $action(
        $this->account,
        [
            'name' => 'My Account Updated',
            'credentials' => [
                'account_number' => 'foo',
                'ftp_username' => 'foo',
                'ftp_password' => 'foo',
            ],
        ]
    );

    expect($this->account->fresh()->name)->toBe('My Account Updated');

    assertSideEffectsTriggered();
});

it('requires a unique account name', function () {
    CarrierAccount::factory()->create(['name' => 'My Other Account']);
    Cache::spy();

    Route::post('/_test', function () {
        $action = $this->action;

        $action(
            $this->account,
            [
                'name' => 'My Other Account',
                'credentials' => [
                    'account_number' => 'foo',
                    'ftp_username' => 'foo',
                    'ftp_password' => 'foo',
                ],
            ]
        );
    });

    post('/_test')
        ->assertSessionHasErrors([
            'name' => 'The name has already been taken.',
        ]);

    expect($this->account->fresh()->name)->toBe('My Account');

    assertSideEffectsNotTriggered();
});

test('unique validation rule does not apply to the same model', function () {
    $action = $this->action;

    Cache::spy();

    $action(
        $this->account,
        [
            'name' => 'My Account',
            'credentials' => [
                'account_number' => 'foo',
                'ftp_username' => 'foo',
                'ftp_password' => 'foo',
            ],
        ]
    );

    assertSideEffectsTriggered();
});

test('the unique validation rule respects the scoped scope on the carrier account model', function () {
    config()->set('easypost.models.carrier_account', CustomCarrierAccount::class);

    $this->account->delete();

    $teamAccount = CustomCarrierAccount::factory()->create([
        'name' => 'Team Account',
        'team_id' => 'my_team',
        'easypost_id' => 'ca_123456',
        'type' => CarrierEnum::Speedee->value,
    ]);
    $otherTeamAccount = CustomCarrierAccount::factory()->create([
        'name' => 'Other Team Account',
        'team_id' => 'other_team',
    ]);

    Route::post('/_test', function (Request $request) use ($teamAccount) {
        $action = $this->action;

        $action(
            $teamAccount,
            [
                'name' => $request->input('name'),
                'credentials' => [
                    'account_number' => 'foo',
                    'ftp_username' => 'foo',
                    'ftp_password' => 'foo',
                ],
            ]
        );
    });

    post('/_test', ['name' => 'Other Team Account'])
        ->assertSuccessful();

    expect($teamAccount->fresh()->name)->toBe('Other Team Account');

    $otherAccount = CustomCarrierAccount::factory()->create([
        'name' => 'My Account',
        'team_id' => 'my_team',
    ]);

    post('/_test', ['name' => 'My Account'])
        ->assertSessionHasErrors([
            'name' => 'The name has already been taken.',
        ]);
});

it('only sends the changed credentials to the api', function () {
    $changes = [
        'credentials' => [
            'account_number' => 'changed',
            'ftp_username' => 'test',
            'ftp_password' => '*******',
        ],
        'test_credentials' => [],
    ];

    $changedValues = invade($this->action)->changedValues($changes);

    expect($changedValues)->toBe([
        'credentials' => [
            'account_number' => 'changed',
        ],
    ]);
});

it('does not send readonly fields to the api', function () {
    // UPS and FedEx both have fields on accounts that are readonly for existing accounts.
    mockProductionApi([
        CarrierTypesMock::make(),
        CarrierAccountMock::make()
            ->forAccountType(CarrierEnum::Ups)
            ->forId('ca_ups'),
        CarrierAccountMock::make()
            ->usingMethod('patch')
            ->forAccountType(CarrierEnum::Ups)
            ->forId('ca_ups'),
    ]);

    $account = CarrierAccount::factory()->make(['name' => 'Mocked Account', 'type' => CarrierEnum::Ups, 'easypost_id' => 'ca_ups']);

    $carrierService = CarrierService::fromAccount('ca_ups');
    $action = app(UpdateCarrierActionContract::class);
    $action
        ->withCarrierService($carrierService)
        ->withStoredValues($carrierService->storedValues());

    $changes = [
        'credentials' => [
            'account_number' => 'changed',
        ],
        'test_credentials' => [],
    ];

    $changedValues = invade($action)->changedValues($changes);

    expect($changedValues)->toBe([]);
});

it('validates the fields for a carrier account type', function (string $fieldToOmit) {
    Cache::spy();

    Route::post('/_test', function () use ($fieldToOmit) {
        $action = $this->action;

        $action(
            $this->account,
            Arr::except([
                'name' => 'My Account Updated',
                'credentials' => [
                    'account_number' => 'foo',
                    'ftp_username' => 'foo',
                    'ftp_password' => 'foo',
                ],
            ], "credentials.{$fieldToOmit}"),
        );
    });

    post('/_test')
        ->assertSessionHasErrors("credentials.{$fieldToOmit}");

    assertSideEffectsNotTriggered();
})->with([
    'account_number',
    'ftp_username',
    'ftp_password',
]);

// Helpers

function assertSideEffectsTriggered(): void
{
    Cache::shouldHaveReceived('forget')->once()->with('easypost::carriers.ca_123456');
    Event::assertDispatched(function (CarrierAccountWasUpdated $event) {
        return $event->account->id === test()->account->id;
    });
}

function assertSideEffectsNotTriggered(): void
{
    Cache::shouldNotHaveReceived('forget');
    Event::assertNotDispatched(CarrierAccountWasUpdated::class);
}
