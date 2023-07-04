<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

final class PublishStubsCommand extends Command
{
    protected $signature = 'easypost:publish-stubs {--force}';

    protected $description = 'Publish the stubs provided by this package.';

    protected Filesystem $filesystem;

    public function __construct()
    {
        parent::__construct();

        $this->filesystem = new Filesystem;
    }

    public function handle(): void
    {
        $this->info('Publishing EasyPost stubs...');

        $this->ensureDirectoriesExist();

        $this->publishStub(
            __DIR__ . '/../../resources/stubs/Policies/CarrierAccountPolicy.php.stub',
            app_path('Policies/CarrierAccountPolicy.php')
        );

        $this->info('Stubs published.');
    }

    protected function ensureDirectoriesExist(): void
    {
        $this->filesystem->ensureDirectoryExists(app_path('Policies'));
    }

    protected function publishStub(string $source, string $destination): void
    {
        if ($this->filesystem->exists($destination) && ! $this->option('force')) {
            return;
        }

        $this->warn("Publishing stub: {$destination}");
        copy($source, $destination);
    }
}
