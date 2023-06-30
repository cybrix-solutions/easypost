<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\TestConcerns;

use Exception;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Gate;

trait UsesDatabase
{
    private static array $migrations = [];

    protected function initDatabase(): void
    {
        $this->setupDatabase();
        $this->setupGates();
    }

    private function setupDatabase(): void
    {
        $this->runMigration(__DIR__ . '/../../database/migrations/create_easypost_tables.php.stub');
        $this->runMigration(__DIR__ . '/../Fixtures/database/migrations/add_team_id_to_carrier_accounts.php');
        $this->runMigration(__DIR__ . '/../Fixtures/database/migrations/create_users_table.php');
    }

    private function runMigration(string $path): void
    {
        if (! isset(static::$migrations[$path])) {
            static::$migrations[$path] = require $path;
        }

        if (static::$migrations[$path] instanceof Migration) {
            static::$migrations[$path]->up();

            return;
        }

        throw new Exception("Couldn't run migration located at {$path}");
    }

    private function setupGates(): void
    {
        // We're not concerned with how the policies work since it should be customized in each application
        Gate::before(function ($user) {
            return $user->email !== 'not-allowed@example.com';
        });
    }
}
