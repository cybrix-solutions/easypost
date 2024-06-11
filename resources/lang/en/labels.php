<?php

declare(strict_types=1);

return [
    'add_carrier_button' => 'Add Carrier',
    'carrier_form_back_button' => 'Back',
    'custom_workflow_help' => 'Please consult [this article](:url) from EasyPost if you need more details on registering this account.',
    'need_carrier_account_title' => 'Need an account?',

    'carrier_account' => [
        'account_id' => 'Account ID: :id',
        'is_easypost_account' => 'Default account provided by EasyPost',
        'is_active' => 'Active',
        'is_inactive' => 'Inactive',
        'is_default' => 'Default',
        'no_results_title' => 'No accounts',
        'no_results_text' => 'Get started with shipping by adding a carrier account.',
    ],

    'carrier_account_form' => [
        'account_details' => 'Account Details',
        'account_name' => 'Account Nickname',
        'account_name_placeholder' => 'Personal',

        'general_add_title' => 'Add Carrier Account',
        'carrier_add_title' => 'Add Your :name Account',
        'add_submit_button' => 'Add Carrier',

        'production_credentials' => 'Production Credentials',
        'test_credentials' => 'Test Credentials (Optional)',

        'select_option_none' => 'None',
        'masked_field_info' => 'Concealed password length does not match actual password length.',

        'accept_tos' => [
            'label' => 'I have read the [license agreement](:url)',
            'validation_label' => 'license agreement',
        ],

        'ups' => [
            'account_info' => 'UPS Account Information',
            'account_info_help' => 'Invoice details are optional for new accounts which have not received their first invoice, otherwise please refer to an invoice issued within the last 90 days. If your submission fails: remove any special characters, double check your account details with UPS, and try again.',
            'company' => 'Company Information',
            'address' => 'Address Information',
        ],

        'fedex' => [
            'address' => 'Address Information',
            'address_help' => 'The "Address Information" must match the address information on your FedEx profile (on the FedEx website). Note that it may not be the same address as your FedEx billing address.',
            'company' => 'Company Information',
            'account_info' => 'Credential Information',
        ],
    ],
];
