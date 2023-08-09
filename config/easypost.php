<?php

declare(strict_types=1);

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
            'ttl' => \DateInterval::createFromDateString('24 hours'),
        ],
        'carrier_account' => [
            // We will replace '{account}' with the actual account id.
            'key' => 'easypost::carriers.{account}',
            'ttl' => \DateInterval::createFromDateString('30 minutes'),
        ],
        'production_webhooks' => [
            'key' => 'easypost::webhooks.production',
            'ttl' => \DateInterval::createFromDateString('24 hours'),
        ],
        'test_webhooks' => [
            'key' => 'easypost::webhooks.test',
            'ttl' => \DateInterval::createFromDateString('24 hours'),
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
        'carrier_account' => \CybrixSolutions\EasyPost\Models\CarrierAccount::class,

        /*
         * This class will be used to store webhook calls from EasyPost. The class should
         * be equal to or extend \CybrixSolutions\EasyPost\Models\WebhookCall.
         */
        'webhook_call' => \CybrixSolutions\EasyPost\Models\WebhookCall::class,

        'shipment' => \CybrixSolutions\EasyPost\Models\Shipment::class,
        'parcel' => \CybrixSolutions\EasyPost\Models\Parcel::class,
        'parcel_tracking' => \CybrixSolutions\EasyPost\Models\ParcelTracking::class,
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
        'add_carrier_account' => \CybrixSolutions\EasyPost\Actions\CarrierAccounts\AddCarrierAccountAction::class,
        'activate_carrier_account' => \CybrixSolutions\EasyPost\Actions\CarrierAccounts\ActivateCarrierAccountAction::class,
        'deactivate_carrier_account' => \CybrixSolutions\EasyPost\Actions\CarrierAccounts\DeactivateCarrierAccountAction::class,
        'make_carrier_default' => \CybrixSolutions\EasyPost\Actions\CarrierAccounts\MakeCarrierDefaultAction::class,
        'delete_carrier_account' => \CybrixSolutions\EasyPost\Actions\CarrierAccounts\DeleteCarrierAction::class,
        'update_carrier_account' => \CybrixSolutions\EasyPost\Actions\CarrierAccounts\UpdateCarrierAction::class,
        'sync_carriers' => \CybrixSolutions\EasyPost\Actions\CarrierAccounts\SyncCarriersAction::class,
        'add_webhook' => \CybrixSolutions\EasyPost\Actions\Webhooks\AddWebhookAction::class,
        'delete_webhook' => \CybrixSolutions\EasyPost\Actions\Webhooks\DeleteWebhookAction::class,
        'update_webhook' => \CybrixSolutions\EasyPost\Actions\Webhooks\UpdateWebhookAction::class,
        'update_parcel_tracking' => \CybrixSolutions\EasyPost\Actions\ParcelTracking\UpdateTrackingAction::class,
        'create_shipment' => \CybrixSolutions\EasyPost\Actions\Shipments\CreateShipmentAction::class,
        'buy_shipment' => \CybrixSolutions\EasyPost\Actions\Shipments\BuyShipmentAction::class,
        'refund_shipment' => \CybrixSolutions\EasyPost\Actions\Shipments\RefundShipmentAction::class,
        'delete_shipment' => \CybrixSolutions\EasyPost\Actions\Shipments\DeleteShipmentAction::class,
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
    'webhook_secret' => env('EASYPOST_WEBHOOK_SECRET'),

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
        'profile' => \CybrixSolutions\EasyPost\Services\Webhooks\DefaultWebhookProfile::class,

        /*
         * This class determines the response on a valid webhook call.
         * In most cases, you shouldn't need to change this.
         */
        'response' => \CybrixSolutions\EasyPost\Services\Webhooks\DefaultWebhookResponse::class,

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
            'refund.successful' => \CybrixSolutions\EasyPost\Jobs\Webhooks\RefundSuccessfulWebhookJob::class,
            'tracker.created' => \CybrixSolutions\EasyPost\Jobs\Webhooks\TrackerCreatedJob::class,
            'tracker.updated' => \CybrixSolutions\EasyPost\Jobs\Webhooks\TrackerUpdatedJob::class,
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
        \CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum::InTransit,
        \CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum::OutForDelivery,
        \CybrixSolutions\EasyPost\Enums\ShipmentStatusEnum::Delivered,
    ],
];
