<?php

declare(strict_types=1);

return [
    'labels' => [
        'add_test_webhook_trigger' => 'Add test webhook',
        'production_webhook' => 'Production Webhook',
        'test_webhook' => 'Test Webhook',
        'webhook_id' => 'ID: :id',
        'inactive' => 'Inactive',
        'delete_button' => 'Delete',
        'configure_production_webhook_trigger' => 'Configure now',
    ],

    'alerts' => [
        'test_added' => 'Test webhook added successfully!',
        'production_added' => 'Production webhook added successfully!',
        'deleted' => 'Webhook was deleted successfully!',
        'missing_production_webhook' => 'No production webhook was found on your account. One must be added for our system to receive shipping updates from EasyPost.',
    ],

    'confirm_delete' => [
        'title' => 'Delete Webhook',
        'text' => "Are you sure you want to delete the webhook with ID **:id**? You will no longer receive important shipping updates if you delete this.\n\nIn most circumstances, you should not delete the webhook. Only do this if you know what you are doing.",
    ],

    'add_test_webhook' => [
        'title' => 'Add Test Webhook',
        'subtitle' => 'Use this form to easily add a test webhook with your desired URL to your EasyPost account. We will send through your configured webhook secret automatically for you.',
        'url' => 'Webhook URL',
        'submit_button' => 'Add webhook',
    ],
];
