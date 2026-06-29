<?php

declare(strict_types=1);
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\ActivateCarrierAccountAction;
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\AddCarrierAccountAction;
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\DeactivateCarrierAccountAction;
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\DeleteCarrierAction;
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\MakeCarrierDefaultAction;
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\SyncCarriersAction;
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\UpdateCarrierAction;
use CybrixSolutions\EasyPost\Actions\ParcelTracking\UpdateTrackingAction;
use CybrixSolutions\EasyPost\Actions\Shipments\BuyShipmentAction;
use CybrixSolutions\EasyPost\Actions\Shipments\CreateShipmentAction;
use CybrixSolutions\EasyPost\Actions\Shipments\DeleteShipmentAction;
use CybrixSolutions\EasyPost\Actions\Shipments\RefundShipmentAction;
use CybrixSolutions\EasyPost\Actions\Webhooks\AddWebhookAction;
use CybrixSolutions\EasyPost\Actions\Webhooks\DeleteWebhookAction;
use CybrixSolutions\EasyPost\Actions\Webhooks\UpdateWebhookAction;
use CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum;
use CybrixSolutions\EasyPost\Jobs\Webhooks\RefundSuccessfulWebhookJob;
use CybrixSolutions\EasyPost\Jobs\Webhooks\TrackerCreatedJob;
use CybrixSolutions\EasyPost\Jobs\Webhooks\TrackerUpdatedJob;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Models\Parcel;
use CybrixSolutions\EasyPost\Models\ParcelTracking;
use CybrixSolutions\EasyPost\Models\Shipment;
use CybrixSolutions\EasyPost\Models\WebhookCall;
use CybrixSolutions\EasyPost\Services\Webhooks\DefaultWebhookProfile;
use CybrixSolutions\EasyPost\Services\Webhooks\DefaultWebhookResponse;
use Filament\Forms\Components\TextInput;

use CybrixSolutions\EasyPost\Actions\CarrierAccounts\ActivateCarrierAccountAction;
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\AddCarrierAccountAction;
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\DeactivateCarrierAccountAction;
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\DeleteCarrierAction;
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\MakeCarrierDefaultAction;
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\SyncCarriersAction;
use CybrixSolutions\EasyPost\Actions\CarrierAccounts\UpdateCarrierAction;
use CybrixSolutions\EasyPost\Actions\ParcelTracking\UpdateTrackingAction;
use CybrixSolutions\EasyPost\Actions\Shipments\BuyShipmentAction;
use CybrixSolutions\EasyPost\Actions\Shipments\CreateShipmentAction;
use CybrixSolutions\EasyPost\Actions\Shipments\DeleteShipmentAction;
use CybrixSolutions\EasyPost\Actions\Shipments\RefundShipmentAction;
use CybrixSolutions\EasyPost\Actions\Webhooks\AddWebhookAction;
use CybrixSolutions\EasyPost\Actions\Webhooks\DeleteWebhookAction;
use CybrixSolutions\EasyPost\Actions\Webhooks\UpdateWebhookAction;
use CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum;
use CybrixSolutions\EasyPost\Jobs\Webhooks\RefundSuccessfulWebhookJob;
use CybrixSolutions\EasyPost\Jobs\Webhooks\TrackerCreatedJob;
use CybrixSolutions\EasyPost\Jobs\Webhooks\TrackerUpdatedJob;
use CybrixSolutions\EasyPost\Models\CarrierAccount;
use CybrixSolutions\EasyPost\Models\Parcel;
use CybrixSolutions\EasyPost\Models\ParcelTracking;
use CybrixSolutions\EasyPost\Models\Shipment;
use CybrixSolutions\EasyPost\Models\WebhookCall;
use CybrixSolutions\EasyPost\Services\Webhooks\DefaultWebhookProfile;
use CybrixSolutions\EasyPost\Services\Webhooks\DefaultWebhookResponse;
use Rawilk\FilamentPasswordInput\Password;

return [
    /*
    |--------------------------------------------------------------------------
    | API Keys
    |--------------------------------------------------------------------------
    |
    | Define your API keys for EasyPost here.
    |
    */
    'api_key' => env('EASYPOST_API_KEY', ''),

    'test_api_key' => env('EASYPOST_TEST_API_KEY', ''),

    'test_mode' => env('EASYPOST_TEST_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Voidable Days
    |--------------------------------------------------------------------------
    |
    | For most carriers, the number of days a package can be voided is the
    | same, however some carriers have different rules.
    |
    | In most cases, you should not modify this.
    |
    */
    'voidable_days' => [
        'default' => 90,
        'usps' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Define some cache settings for when we need to retrieve certain data
    | from the EasyPost API.
    |
    */
    'cache' => [
        'carriers' => [
            'key' => 'easypost::carriers',
            'ttl' => DateInterval::createFromDateString('24 hours'),
        ],
        'carrier_account' => [
            // We will replace '{account}' with the actual account id.
            'key' => 'easypost::carriers.{account}',
            'ttl' => DateInterval::createFromDateString('1 month'),
        ],
        'production_webhooks' => [
            'key' => 'easypost::webhooks.production',
            'ttl' => DateInterval::createFromDateString('24 hours'),
        ],
        'test_webhooks' => [
            'key' => 'easypost::webhooks.test',
            'ttl' => DateInterval::createFromDateString('24 hours'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Define the Eloquent models used by the package.
    |
    */
    'models' => [
        'carrier_account' => CarrierAccount::class,

        /*
         * This class will be used to store webhook calls from EasyPost. The class should
         * be equal to or extend \CybrixSolutions\EasyPost\Models\WebhookCall.
         */
        'webhook_call' => WebhookCall::class,

        'shipment' => Shipment::class,
        'parcel' => Parcel::class,
        'parcel_tracking' => ParcelTracking::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Tables
    |--------------------------------------------------------------------------
    |
    | Define the database tables used by the package.
    |
    */
    'table_names' => [
        'carrier_accounts' => 'carrier_accounts',
        'webhook_calls' => 'easypost_webhook_calls',
        'shipments' => 'shipments',
        'parcels' => 'parcels',
        'parcel_tracking' => 'parcel_tracking',

        /*
         * This is mostly for us internally since we had an existing schema with a different fk name
         * than a sensible default in this package.
         */
        'parcel_tracking_parcel_fk' => 'parcel_id',
    ],

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    |
    | Here you may define the actions that are used for various events, such
    | as adding a carrier account.
    |
    | Note: If you choose to use your own class, it must implement the
    | interface that the original action class implements.
    |
    */
    'actions' => [
        'add_carrier_account' => AddCarrierAccountAction::class,
        'activate_carrier_account' => ActivateCarrierAccountAction::class,
        'deactivate_carrier_account' => DeactivateCarrierAccountAction::class,
        'make_carrier_default' => MakeCarrierDefaultAction::class,
        'delete_carrier_account' => DeleteCarrierAction::class,
        'update_carrier_account' => UpdateCarrierAction::class,
        'sync_carriers' => SyncCarriersAction::class,
        'add_webhook' => AddWebhookAction::class,
        'delete_webhook' => DeleteWebhookAction::class,
        'update_webhook' => UpdateWebhookAction::class,
        'update_parcel_tracking' => UpdateTrackingAction::class,
        'create_shipment' => CreateShipmentAction::class,
        'buy_shipment' => BuyShipmentAction::class,
        'refund_shipment' => RefundShipmentAction::class,
        'delete_shipment' => DeleteShipmentAction::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | Define a prefix for the routes defined by the package.
    |
    */
    'route_prefix' => '/easypost',

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | Define the secret we will use to secure webhooks from EasyPost.
    |
    */
    'webhook_secret' => env('EASYPOST_WEBHOOK_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Webhooks URL
    |--------------------------------------------------------------------------
    |
    | Define the URL path that EasyPost will send webhooks to.
    |
    */
    'webhook_url' => '/webhooks/easypost',

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Define how the package will handle incoming webhooks.
    |
    */
    'webhook_config' => [
        /*
         * This class determines if the webhook call should be stored and processed.
         */
        'profile' => DefaultWebhookProfile::class,

        /*
         * This class determines the response on a valid webhook call.
         * In most cases, you shouldn't need to change this.
         */
        'response' => DefaultWebhookResponse::class,

        /*
         * In this array, you can pass the headers that should be stored on
         * the webhook call model when a webhook is received.
         *
         * To store all headers, set this value to '*'.
         */
        'store_headers' => [],

        /*
         * Define jobs to handle certain webhook events, such as a shipment's tracking being updated.
         */
        'processors' => [
            'refund.successful' => RefundSuccessfulWebhookJob::class,
            'tracker.created' => TrackerCreatedJob::class,
            'tracker.updated' => TrackerUpdatedJob::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Retention
    |--------------------------------------------------------------------------
    |
    | Define how long we will retain webhook calls in the database.
    | Set to null if no records should be deleted.
    |
    */
    'webhook_retention_days' => 30,

    /*
    |--------------------------------------------------------------------------
    | Notifiable Shipment Statuses
    |--------------------------------------------------------------------------
    |
    | Determine which statuses should be able to trigger notifications.
    |
    */
    'notifiable_shipment_statuses' => [
        ShipmentStatusEnum::InTransit,
        ShipmentStatusEnum::OutForDelivery,
        ShipmentStatusEnum::Delivered,
    ],

    /*
    |--------------------------------------------------------------------------
    | Filament Settings
    |--------------------------------------------------------------------------
    |
    */
    'filament' => [
        'password_component' => Password::class,
    ],

];
