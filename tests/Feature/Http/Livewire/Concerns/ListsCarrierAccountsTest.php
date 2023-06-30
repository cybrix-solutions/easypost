<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\Livewire\TestCarrierAccountList;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\User;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

uses(UsesDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);

    $this->accounts = CarrierAccount::factory()->count(3)->create();
});

it('can be rendered', function () {
    livewire(TestCarrierAccountList::class)
        ->assertSuccessful()
        ->assertSee($this->accounts[0]->name)
        ->assertSee($this->accounts[1]->name)
        ->assertSee($this->accounts[2]->name);
});
