<?php

namespace CybrixSolutions\EasyPost\Tests;

use CybrixSolutions\EasyPost\EasyPostServiceProvider;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\CustomCarrierAccount;
use CybrixSolutions\EasyPost\Tests\Fixtures\Models\User;
use CybrixSolutions\EasyPost\Tests\Fixtures\Policies\CarrierAccountPolicy;
use CybrixSolutions\EasyPost\Tests\TestConcerns\UsesDatabase;
use Dotenv\Dotenv;
use Illuminate\Support\Facades\Gate;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected $loadEnvironmentVariables = true;

    protected $enablesPackageDiscoveries = true;

    protected function setUp(): void
    {
        $this->loadEnvironmentVariables();

        parent::setUp();

        if ($this->needsDatabase()) {
            $this->initDatabase();
        }
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('easypost.table_names.carrier_accounts', 'carrier_accounts');
        config()->set('easypost.models.carrier_account', CarrierAccount::class);
        config()->set('auth.providers.users.model', User::class);

        Gate::policy(CarrierAccount::class, CarrierAccountPolicy::class);
        Gate::policy(CustomCarrierAccount::class, CarrierAccountPolicy::class);
    }

    protected function getPackageProviders($app): array
    {
        return [
            EasyPostServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }

    protected function loadEnvironmentVariables(): void
    {
        if (! file_exists(__DIR__ . '/../.env')) {
            return;
        }

        $dotEnv = Dotenv::createImmutable(__DIR__ . '/..');

        $dotEnv->load();
    }

    protected function needsDatabase(): bool
    {
        return in_array(UsesDatabase::class, class_uses_recursive(static::class), true);
    }
}
