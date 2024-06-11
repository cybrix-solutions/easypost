<?php

declare(strict_types=1);

return [

    'heading' => 'Webhooks',
    'description' => 'Webhooks allow our system to receive updates on tracking for shipments from EasyPost. In most cases, you should not need to modify anything in this section.',

    'modes' => [
        'production' => 'Production',
        'test' => 'Test',
    ],

    'table' => [
        'url' => [
            'label' => 'Hook',
        ],

        'mode' => [
            'label' => 'Mode',
        ],

        'id' => [
            'label' => 'ID: :id',
        ],

        'empty' => [
            'heading' => 'No webhooks found',
            'description' => 'Without a production webhook on your EasyPost account, we will not be able to receive and process updates for your shipments. Please configure a production webhook to receive shipping updates.',
        ],
    ],

    'actions' => [
        'add' => [
            'label' => 'Add webhook',
            'heading' => 'Add test webhook',
            'modal_submit' => 'Add webhook',

            'url' => [
                'label' => 'Webhook URL',
                'placeholder' => 'https://example.com/webhooks/easypost',
                'helper_text' => 'We will automatically configure a webhook secret for this URL.',
            ],

            'success' => 'Test webhook added successfully.',
        ],

        'delete' => [
            'heading' => 'Delete Webhook',
            'content' => 'Our system will no longer process EasyPost webhooks sent to _:url_. The webhook will also be deleted from your EasyPost account.',
        ],

        'configure_production' => [
            'label' => 'Configure now',
            'success' => 'Production webhook configured successfully.',
        ],
    ],

    'production' => [
        'missing' => [
            'description' => 'No production webhook was found on your account. One must be added for our system to receive shipping updates from EasyPost.',
        ],
    ],

];
