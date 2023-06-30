<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Tests\Fixtures\Models\CustomCarrierAccount;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;

uses(UsesDatabase::class);

test('otherInactiveAccounts scope applies scoped scope', function () {
    $teamAccounts = CustomCarrierAccount::factory()->inactive()->count(3)->create([
        'team_id' => 'my_team',
    ]);
    $otherTeamAccount = CustomCarrierAccount::factory()->inactive()->create([
        'team_id' => 'other_team',
    ]);
    $nonTeamAccount = CustomCarrierAccount::factory()->inactive()->create();

    $this->assertDatabaseCount(CustomCarrierAccount::class, 5);

    $scoped = CustomCarrierAccount::otherInactiveAccounts($teamAccounts->first())->get();

    expect($scoped)->toHaveCount(2)
        ->and($scoped[0]->id)->toBe($teamAccounts[1]->id)
        ->and($scoped[1]->id)->toBe($teamAccounts[2]->id)
        ->and($scoped[0]->team_id)->toBe('my_team');
});

test('otherDefaultedAccounts scope applies scoped scope', function () {
    $teamAccount = CustomCarrierAccount::factory()->create([
        'team_id' => 'my_team',
    ]);
    $defaultTeamAccount = CustomCarrierAccount::factory()->isDefault()->create([
        'team_id' => 'my_team',
    ]);
    $otherTeamAccount = CustomCarrierAccount::factory()->isDefault()->create([
        'team_id' => 'other_team',
    ]);

    $this->assertDatabaseCount(CustomCarrierAccount::class, 3);

    expect(CustomCarrierAccount::where('default', true)->count())->toBe(2);

    $scoped = CustomCarrierAccount::otherDefaultedAccounts($teamAccount)->get();

    expect($scoped)->toHaveCount(1)
        ->and($scoped[0]->id)->toBe($defaultTeamAccount->id);
});

test('otherActiveAccounts scope applies scoped scope', function () {
    $teamAccounts = CustomCarrierAccount::factory()->count(3)->create([
        'team_id' => 'my_team',
    ]);
    $otherTeamAccount = CustomCarrierAccount::factory()->create([
        'team_id' => 'other_team',
    ]);
    $nonTeamAccount = CustomCarrierAccount::factory()->create();

    $this->assertDatabaseCount(CustomCarrierAccount::class, 5);

    $scoped = CustomCarrierAccount::otherActiveAccounts($teamAccounts->first())->get();

    expect($scoped)->toHaveCount(2)
        ->and($scoped[0]->id)->toBe($teamAccounts[1]->id)
        ->and($scoped[1]->id)->toBe($teamAccounts[2]->id)
        ->and($scoped[0]->team_id)->toBe('my_team');
});

it('respects the scoped scope when updating default statuses on account creation', function () {
    $teamAccount = CustomCarrierAccount::factory()->isDefault()->create([
        'team_id' => 'my_team',
    ]);
    $otherTeamAccount = CustomCarrierAccount::factory()->isDefault()->create([
        'team_id' => 'other_team',
    ]);
    $nonTeamAccount = CustomCarrierAccount::factory()->isDefault()->create();

    expect($teamAccount->fresh()->default)->toBeTrue()
        ->and($otherTeamAccount->fresh()->default)->toBeTrue()
        ->and($nonTeamAccount->fresh()->default)->toBeTrue();

    $newTeamAccount = CustomCarrierAccount::factory()->isDefault()->create([
        'team_id' => 'my_team',
    ]);

    expect($newTeamAccount->fresh()->default)->toBeTrue()
        ->and($teamAccount->fresh()->default)->toBeFalse()
        ->and($otherTeamAccount->fresh()->default)->toBeTrue()
        ->and($nonTeamAccount->fresh()->default)->toBeTrue();
});
