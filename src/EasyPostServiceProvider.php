<?php

namespace CybrixSolutions\EasyPost;

use CybrixSolutions\EasyPost\Commands\EasyPostCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EasyPostServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('easypost')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_easypost_table')
            ->hasCommand(EasyPostCommand::class);
    }
}
