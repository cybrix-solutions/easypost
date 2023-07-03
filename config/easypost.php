<?php

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
            'ttl' => 60 * 60 * 24, // 1 day
        ],
        'carrier_account' => [
            // We will replace '{account}' with the actual account id.
            'key' => 'easypost::carriers.{account}',
            'ttl' => 60 * 30, // 30 minutes
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
        'add_carrier_account' => \App\Actions\EasyPost\AddCarrierAccountAction::class,
        'activate_carrier_account' => \CybrixSolutions\EasyPost\Actions\CarrierAccounts\ActivateCarrierAccountAction::class,
        'deactivate_carrier_account' => \CybrixSolutions\EasyPost\Actions\CarrierAccounts\DeactivateCarrierAccountAction::class,
        'make_carrier_default' => \CybrixSolutions\EasyPost\Actions\CarrierAccounts\MakeCarrierDefaultAction::class,
        'delete_carrier_account' => \CybrixSolutions\EasyPost\Actions\CarrierAccounts\DeleteCarrierAction::class,
        'update_carrier_account' => \CybrixSolutions\EasyPost\Actions\CarrierAccounts\UpdateCarrierAction::class,
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
];
