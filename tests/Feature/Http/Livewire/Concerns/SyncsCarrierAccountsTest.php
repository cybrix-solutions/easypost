<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\CarrierAccounts\SyncCarriersAction;
use CybrixSolutions\EasyPost\Events\CarrierAccountWasCreated;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\CarrierAccounts\CarrierAccountsListMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\Livewire\TestCarrierSyncComponent;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\CustomCarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\User;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

uses(UsesDatabase::class);

beforeEach(function () {
    config()->set('easypost.actions.sync_carriers', SyncCarriersAction::class);

    mockProductionApi([
        CarrierAccountsListMock::make(),
    ]);

    $this->user = User::factory()->create();

    actingAs($this->user);
});

it('can confirm syncing carrier accounts', function () {
    livewire(TestCarrierSyncComponent::class)
        ->assertSeeText('Button to sync')
        ->call('confirm')
        ->assertSet('show', true)
        ->assertSet('errorMessage', null)
        ->assertSeeText('Are you sure?')
        ->assertDontSeeText('Button to sync');
});

it('can sync carrier accounts', function () {
    Event::fake();

    $this->assertDatabaseCount(CarrierAccount::class, 0);

    livewire(TestCarrierSyncComponent::class)
        ->call('confirm')
        ->call('sync')
        ->assertSuccessful()
        ->assertSet('show', false)
        ->assertSet('errorMessage', null)
        ->assertEmitted('carrier_account.synced');

    $this->assertDatabaseCount(CarrierAccount::class, 2);

    $this->assertDatabaseHas(CarrierAccount::class, [
        'easypost_id' => 'ca_speedee',
        'name' => 'Spee-Dee Mocked Account',
    ]);

    $this->assertDatabaseHas(CarrierAccount::class, [
        'easypost_id' => 'ca_ups',
        'name' => 'UPS Mocked Account',
    ]);

    Event::assertDispatchedTimes(CarrierAccountWasCreated::class, 2);
});

it('applies custom context to the sync action', function () {
    config()->set('easypost.models.carrier_account', CustomCarrierAccount::class);

    livewire(TestSyncWithContext::class)
        ->call('confirm')
        ->call('sync')
        ->assertSuccessful();

    $this->assertDatabaseCount(CustomCarrierAccount::class, 2);

    $this->assertDatabaseHas(CustomCarrierAccount::class, [
        'easypost_id' => 'ca_speedee',
        'team_id' => 'my_team',
    ]);

    $this->assertDatabaseHas(CustomCarrierAccount::class, [
        'easypost_id' => 'ca_ups',
        'team_id' => 'my_team',
    ]);
});

it('applies a custom account filter to the sync action', function () {
    config()->set('easypost.models.carrier_account', CustomCarrierAccount::class);

    livewire(TestSyncWithFilter::class)
        ->call('confirm')
        ->call('sync')
        ->assertSuccessful();

    $this->assertDatabaseCount(CustomCarrierAccount::class, 1);

    $this->assertDatabaseHas(CustomCarrierAccount::class, [
        'easypost_id' => 'ca_ups',
        'team_id' => 'my_team',
    ]);
});

it('does nothing if there is not a production api key set', function () {
    config()->set('easypost.api_key', '');

    livewire(TestCarrierSyncComponent::class)
        ->call('confirm')
        ->assertSet('show', false)
        ->call('sync')
        ->assertNotEmitted('carrier_account.synced');
});

it('authorizes a user to sync', function () {
    actingAs(User::factory()->notAllowed()->create());

    livewire(TestCarrierSyncComponent::class)
        ->call('confirm')
        ->call('sync')
        ->assertForbidden();
});

it('allows extra context for authorization', function () {
    $policy = new class
    {
        use HandlesAuthorization;

        public function sync($user, string $teamId = ''): bool
        {
            return $teamId !== 'other_team';
        }
    };

    Gate::policy(CarrierAccount::class, $policy::class);

    livewire(TestSyncWithAuthorization::class)
        ->call('confirm')
        ->call('sync')
        ->assertSuccessful()
        ->call('setTeam', 'other_team')
        ->call('confirm')
        ->call('sync')
        ->assertForbidden();
});

test('extra code can be executed once accounts have been synced', function () {
    livewire(TestCarrierSyncComponent::class)
        ->assertDontSeeText('Synced!')
        ->call('confirm')
        ->call('sync')
        ->assertSuccessful()
        ->assertSet('show', false)
        ->assertSet('errorMessage', null)
        ->assertEmitted('carrier_account.synced')
        ->assertSeeText('Synced!');
});

// Helpers

class TestSyncWithContext extends TestCarrierSyncComponent
{
    protected function syncContext(): array
    {
        return [
            'team_id' => 'my_team',
        ];
    }
}

class TestSyncWithFilter extends TestSyncWithContext
{
    protected function syncFilter(): ?callable
    {
        return function (EasyPost\CarrierAccount $account) {
            return $account->id === 'ca_ups';
        };
    }
}

class TestSyncWithAuthorization extends TestSyncWithContext
{
    public string $teamId = 'my_team';

    public function setTeam(string $teamId): void
    {
        $this->teamId = $teamId;
    }

    protected function authorizeSyncWith(): array
    {
        return [$this->teamId];
    }
}
