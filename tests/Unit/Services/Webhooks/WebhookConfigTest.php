<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Exceptions\Webhooks\InvalidWebhookConfig;
use CybrixSolutions\EasyPost\Models\WebhookCall;
use CybrixSolutions\EasyPost\Services\Webhooks\DefaultWebhookProfile;
use CybrixSolutions\EasyPost\Services\Webhooks\DefaultWebhookResponse;
use CybrixSolutions\EasyPost\Services\Webhooks\WebhookConfig;

it('can handle a valid configuration', function () {
    $config = validWebhookConfig();

    $webhookConfig = new WebhookConfig($config);

    expect($webhookConfig->signingSecret)->toBe($config['signing_secret'])
        ->and($webhookConfig->signatureHeaderName)->toBe($config['signature_header_name'])
        ->and($webhookConfig->webhookProfile)->toBeInstanceOf($config['profile'])
        ->and($webhookConfig->webhookCallModel)->toBe($config['webhook_call_model']);
});

it('validates the webhook profile', function () {
    $config = validWebhookConfig();
    $config['profile'] = 'invalid';

    new WebhookConfig($config);
})->throws(InvalidWebhookConfig::class);

it('validates the webhook response', function () {
    $config = validWebhookConfig();
    $config['response'] = 'invalid';

    new WebhookConfig($config);
})->throws(InvalidWebhookConfig::class);

it('uses the default webhook response if none is provided', function () {
    $config = validWebhookConfig();
    $config['response'] = null;

    $webhookConfig = new WebhookConfig($config);

    expect($webhookConfig->webhookResponse)->toBeInstanceOf(DefaultWebhookResponse::class);
});

// Helpers

function validWebhookConfig(): array
{
    return [
        'signing_secret' => 'my-secret',
        'signature_header_name' => 'X-Hmac-Signature',
        'webhook_call_model' => WebhookCall::class,
        'profile' => DefaultWebhookProfile::class,
        'response' => DefaultWebhookResponse::class,
        'store_headers' => [],
        'process_webhook_job' => '',
    ];
}
