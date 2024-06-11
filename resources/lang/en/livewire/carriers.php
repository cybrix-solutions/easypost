<?php

declare(strict_types=1);

return [

    'accounts' => [
        'heading' => 'Carrier Accounts',
        'description' => 'A carrier account will be used to send your shipments through. We will never store your shipping account credentials on our servers.',

        'production_api_key_required' => 'A production API key for EasyPost must be entered before you can add carrier accounts.',

        'table' => [
            'name' => [
                'label' => 'Account',
            ],

            'easypost_id' => [
                'description' => 'Account ID: :id',
            ],

            'carrier' => [
                'label' => 'Carrier',
            ],

            'empty_state' => [
                'title' => 'No carrier accounts',
                'description_with_search' => 'No carrier accounts were found that match your search.',
                'description_without_search' => 'Get started with shipping by adding a carrier account',
            ],
        ],

        'actions' => [
            'create' => [
                'label' => 'Add carrier account',
                'modal_heading' => 'Add carrier account',
                'modal_submit' => 'Add carrier',

                'search' => [
                    'placeholder' => 'Search by carrier name',
                ],
            ],

            'delete' => [
                'heading' => 'Delete carrier account',
                'description' => 'Are you sure you want to delete your :carrier account with ID **:id**?',
                'warning' => 'Deleting a Carrier account will make it so that you can **no longer track, refund, cancel, or manifest shipments** created using that carrier account. It will also **disassociate any rate tables** loaded on it. If you are having technical issues or have questions please contact support@easypost.com first.',
            ],

            'make_default' => [
                'label' => 'Make default',
                'success' => ':name was made the default account.',
            ],

            'activate' => [
                'label' => 'Activate',
                'success' => ':name was activated.',
            ],

            'deactivate' => [
                'label' => 'Deactivate',
                'success' => ':name was deactivated.',
            ],

            'edit' => [
                'heading' => ':type Account',
                'account_not_found' => 'We were unable to retrieve your account details from EasyPost.',
            ],

            'sync' => [
                'label' => 'Sync carrier accounts',
                'description' => 'We will attempt to sync any carrier accounts that were created outside of this dashboard. Do you wish to proceed?',
                'modal_submit' => 'Yes',
                'success' => 'Carrier account sync completed.',
            ],
        ],
    ],

];
