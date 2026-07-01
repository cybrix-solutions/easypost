<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost;

use CybrixSolutions\EasyPost\Commands\GenerateWebhookSecretCommand;
use CybrixSolutions\EasyPost\Commands\PublishAssetsCommand;
use CybrixSolutions\EasyPost\Commands\PublishStubsCommand;
use CybrixSolutions\EasyPost\Commands\UpdateWebhookSecretsCommand;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\ActivateCarrierAccountAction as ActivateCarrierAccountActionContract;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\AddCarrierAccountAction as AddCarrierAccountContract;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\DeactivateCarrierAccountAction as DeactivateCarrierAccountActionContract;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\DeleteCarrierAction as DeleteCarrierActionContract;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\MakeCarrierDefaultAction as MakeCarrierDefaultActionContract;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\SyncCarriersAction as SyncCarriersActionContract;
use CybrixSolutions\EasyPost\Contracts\CarrierAccounts\UpdateCarrierAction as UpdateCarrierActionContract;
use CybrixSolutions\EasyPost\Contracts\Models\CarrierAccount as CarrierAccountContract;
use CybrixSolutions\EasyPost\Contracts\Models\Parcel as ParcelContract;
use CybrixSolutions\EasyPost\Contracts\Models\ParcelTracking as ParcelTrackingContract;
use CybrixSolutions\EasyPost\Contracts\Models\Shipment as ShipmentContract;
use CybrixSolutions\EasyPost\Contracts\ParcelTracking\UpdateTrackingAction as UpdateTrackingActionContract;
use CybrixSolutions\EasyPost\Contracts\Shipments\BuyShipmentAction as BuyShipmentActionContract;
use CybrixSolutions\EasyPost\Contracts\Shipments\CreateShipmentAction as CreateShipmentActionContract;
use CybrixSolutions\EasyPost\Contracts\Shipments\DeleteShipmentAction as DeleteShipmentActionContract;
use CybrixSolutions\EasyPost\Contracts\Shipments\RefundShipmentAction as RefundShipmentActionContract;
use CybrixSolutions\EasyPost\Contracts\Webhooks\AddWebhookAction as AddWebhookActionContract;
use CybrixSolutions\EasyPost\Contracts\Webhooks\DeleteWebhookAction as DeleteWebhookActionContract;
use CybrixSolutions\EasyPost\Contracts\Webhooks\UpdateWebhookAction as UpdateWebhookActionContract;
use CybrixSolutions\EasyPost\Facades\EasyPost as EasyPostFacade;
use CybrixSolutions\EasyPost\Services\Api\EasyPostClient;
use CybrixSolutions\EasyPost\Services\Api\ProductionEasyPostClient;
use CybrixSolutions\EasyPost\Services\Webhooks\WebhookConfig;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class EasyPostServiceProvider extends PackageServiceProvider
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
            ->hasCommands([
                PublishStubsCommand::class,
                PublishAssetsCommand::class,
                GenerateWebhookSecretCommand::class,
                UpdateWebhookSecretsCommand::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->publishAssets()
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->endWith(function (InstallCommand $command) {
                        $command->call(PublishStubsCommand::class);
                        $command->call(GenerateWebhookSecretCommand::class);
                    });
            });
    }

    public function packageBooted(): void
    {
        $this
            ->bootApi()
            ->bootAboutCommand()
            ->bootBladeComponents()
            ->bootModelBindings()
            ->bootClassBindings()
            ->bootLivewireComponents();
    }

    public function packageRegistered(): void
    {
        $this->app->register(EventServiceProvider::class);
    }

    /**
     * We are using two separate instances for the EasyPostClient because
     * there are times when a production API key must be used, even when
     * in a test environment.
     */
    protected function bootApi(): self
    {
        $this->app->scoped(EasyPostClient::class, function () {
            return new EasyPostClient(
                EasyPostFacade::inTestMode()
                    ? EasyPostFacade::testApiKey()
                    : EasyPostFacade::apiKey()
            );
        });

        $this->app->scoped(
            ProductionEasyPostClient::class,
            fn () => new ProductionEasyPostClient(EasyPostFacade::apiKey()),
        );

        // We are not referencing our Client wrappers here because we need to ensure that the webhooks
        // go into the environment they are supposed to be when they are created.
        $this->app->scoped(
            WebhooksService::class,
            fn () => new WebhooksService(
                EasyPostFacade::apiKey(),
                EasyPostFacade::testApiKey(),
                (string) config('easypost.webhook_secret', ''),
                (string) config('easypost.webhook_url', ''),
            ),
        );

        $this->app->scoped(WebhookConfig::class, fn () => new WebhookConfig([
            'signing_secret' => config('easypost.webhook_secret'),
            'signature_header_name' => 'X-Hmac-Signature',
            'webhook_call_model' => config('easypost.models.webhook_call'),
            ...config('easypost.webhook_config'),
        ]));

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
        $this->app->bind(ShipmentContract::class, fn ($app) => $app->make(config('easypost.models.shipment')));
        $this->app->bind(ParcelContract::class, fn ($app) => $app->make(config('easypost.models.parcel')));
        $this->app->bind(ParcelTrackingContract::class, fn ($app) => $app->make(config('easypost.models.parcel_tracking')));

        return $this;
    }

    protected function bootClassBindings(): self
    {
        // Carrier Accounts
        $this->app->bind(AddCarrierAccountContract::class, fn ($app) => $app->make(config('easypost.actions.add_carrier_account')));
        $this->app->bind(ActivateCarrierAccountActionContract::class, fn ($app) => $app->make(config('easypost.actions.activate_carrier_account')));
        $this->app->bind(DeactivateCarrierAccountActionContract::class, fn ($app) => $app->make(config('easypost.actions.deactivate_carrier_account')));
        $this->app->bind(MakeCarrierDefaultActionContract::class, fn ($app) => $app->make(config('easypost.actions.make_carrier_default')));
        $this->app->bind(DeleteCarrierActionContract::class, fn ($app) => $app->make(config('easypost.actions.delete_carrier_account')));
        $this->app->bind(UpdateCarrierActionContract::class, fn ($app) => $app->make(config('easypost.actions.update_carrier_account')));
        $this->app->bind(SyncCarriersActionContract::class, fn ($app) => $app->make(config('easypost.actions.sync_carriers')));

        // Webhooks
        $this->app->bind(AddWebhookActionContract::class, fn ($app) => $app->make(config('easypost.actions.add_webhook')));
        $this->app->bind(DeleteWebhookActionContract::class, fn ($app) => $app->make(config('easypost.actions.delete_webhook')));
        $this->app->bind(UpdateWebhookActionContract::class, fn ($app) => $app->make(config('easypost.actions.update_webhook')));

        // Parcel Tracking
        $this->app->bind(UpdateTrackingActionContract::class, fn ($app) => $app->make(config('easypost.actions.update_parcel_tracking')));

        // Shipments
        $this->app->bind(CreateShipmentActionContract::class, fn ($app) => $app->make(config('easypost.actions.create_shipment')));
        $this->app->bind(BuyShipmentActionContract::class, fn ($app) => $app->make(config('easypost.actions.buy_shipment')));
        $this->app->bind(RefundShipmentActionContract::class, fn ($app) => $app->make(config('easypost.actions.refund_shipment')));
        $this->app->bind(DeleteShipmentActionContract::class, fn ($app) => $app->make(config('easypost.actions.delete_shipment')));

        return $this;
    }

    private function bootLivewireComponents(): self
    {
        if (! class_exists(Livewire::class)) {
            return $this;
        }

        Livewire::addNamespace('easypost', classNamespace: __NAMESPACE__ . '\\Livewire');

        return $this;
    }
}
