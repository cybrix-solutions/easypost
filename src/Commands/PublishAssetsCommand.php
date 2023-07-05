<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * We are wrapping the publish assets command, so we can also delete the current
 * carrier images before re-publishing them at the same time.
 */
final class PublishAssetsCommand extends Command
{
    protected $signature = 'easypost:publish-assets';

    protected $description = 'Publish the assets provided by this package.';

    public function handle(): void
    {
        if (File::isDirectory(public_path('vendor/easypost'))) {
            File::deleteDirectory(public_path('vendor/easypost'));
        }

        $this->call('vendor:publish', [
            '--tag' => 'easypost-assets',
        ]);
    }
}
