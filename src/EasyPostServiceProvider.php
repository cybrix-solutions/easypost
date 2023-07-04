<?php

namespace CybrixSolutions\EasyPost;

use CybrixSolutions\EasyPost\Commands\PublishStubsCommand;
use CybrixSolutions\EasyPost\Contracts\ActivateCarrierAccountAction as ActivateCarrierAccountActionContract;
use CybrixSolutions\EasyPost\Contracts\AddCarrierAccountAction as AddCarrierAccountContract;
use CybrixSolutions\EasyPost\Contracts\CarrierAccount as CarrierAccountContract;
use CybrixSolutions\EasyPost\Contracts\DeactivateCarrierAccountAction as DeactivateCarrierAccountActionContract;
use CybrixSolutions\EasyPost\Contracts\DeleteCarrierAction as DeleteCarrierActionContract;
use CybrixSolutions\EasyPost\Contracts\MakeCarrierDefaultAction as MakeCarrierDefaultActionContract;
use CybrixSolutions\EasyPost\Contracts\SyncCarriersAction as SyncCarriersActionContract;
use CybrixSolutions\EasyPost\Contracts\UpdateCarrierAction as UpdateCarrierActionContract;
use CybrixSolutions\EasyPost\Facades\EasyPost as EasyPostFacade;
use CybrixSolutions\EasyPost\Services\Api\EasyPostClient;
use CybrixSolutions\EasyPost\Services\Api\ProductionEasyPostClient;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EasyPostServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('easypost')
            ->hasConfigFile()
            ->hasAssets()
            ->hasTranslations()
            ->hasViews()
            ->hasRoute('web')
            ->hasMigration('create_easypost_tables')
            ->hasCommand(PublishStubsCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->publishAssets()
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->endWith(function (InstallCommand $command) {
                        $command->call(PublishStubsCommand::class);
                    });
            });
    }

    public function packageBooted(): void
    {
        $this->bootApi()
            ->bootAboutCommand()
            ->bootBladeComponents()
            ->bootModelBindings()
            ->bootClassBindings();
    }

    /**
     * We are using two separate instances for the EasyPostClient because
     * there are times when a production API key must be used, even when
     * in a test environment.
     */
    protected function bootApi(): self
    {
        $this->app->singleton(EasyPostClient::class, function () {
            $apiKey = EasyPostFacade::inTestMode()
                ? EasyPostFacade::testApiKey()
                : EasyPostFacade::apiKey();

            return new EasyPostClient($apiKey);
        });

        $this->app->singleton(
            ProductionEasyPostClient::class,
            fn () => new ProductionEasyPostClient(EasyPostFacade::apiKey()),
        );

        return $this;
    }

    protected function bootAboutCommand(): self
    {
        AboutCommand::add('EasyPost Service', fn () => [
            'Version' => EasyPost::VERSION,
        ]);

        return $this;
    }

    protected function bootBladeComponents(): self
    {
        Blade::componentNamespace('CybrixSolutions\\EasyPost\\View\\Components', 'easypost');

        return $this;
    }

    protected function bootModelBindings(): self
    {
        $this->app->bind(CarrierAccountContract::class, fn ($app) => $app->make(config('easypost.models.carrier_account')));

        return $this;
    }

    protected function bootClassBindings(): self
    {
        $this->app->bind(AddCarrierAccountContract::class, fn ($app) => $app->make(config('easypost.actions.add_carrier_account')));
        $this->app->bind(ActivateCarrierAccountActionContract::class, fn ($app) => $app->make(config('easypost.actions.activate_carrier_account')));
        $this->app->bind(DeactivateCarrierAccountActionContract::class, fn ($app) => $app->make(config('easypost.actions.deactivate_carrier_account')));
        $this->app->bind(MakeCarrierDefaultActionContract::class, fn ($app) => $app->make(config('easypost.actions.make_carrier_default')));
        $this->app->bind(DeleteCarrierActionContract::class, fn ($app) => $app->make(config('easypost.actions.delete_carrier_account')));
        $this->app->bind(UpdateCarrierActionContract::class, fn ($app) => $app->make(config('easypost.actions.update_carrier_account')));
        $this->app->bind(SyncCarriersActionContract::class, fn ($app) => $app->make(config('easypost.actions.sync_carriers')));

        return $this;
    }
}
