<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Webhooks\RespondsToWebhook;
use CybrixSolutions\EasyPost\Contracts\Webhooks\WebhookProfile;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\InvalidWebhookConfig;

final class WebhookConfig
{
    public string $signingSecret;

    public string $signatureHeaderName;

    public WebhookProfile $webhookProfile;

    public RespondsToWebhook $webhookResponse;

    public array|string $storeHeaders;

    public string $webhookCallModel;

    public function __construct(array $properties)
    {
        $this->signingSecret = $properties['signing_secret'] ?? '';

        $this->signatureHeaderName = $properties['signature_header_name'] ?? '';

        if (! is_subclass_of($properties['profile'], WebhookProfile::class)) {
            throw InvalidWebhookConfig::invalidWebhookProfile($properties['profile']);
        }
        $this->webhookProfile = app($properties['profile']);

        $responseClass = $properties['response'] ?? DefaultWebhookResponse::class;
        if (! is_subclass_of($responseClass, RespondsToWebhook::class)) {
            throw InvalidWebhookConfig::invalidWebhookResponse($responseClass);
        }
        $this->webhookResponse = app($responseClass);

        $this->storeHeaders = $properties['store_headers'] ?? [];

        $this->webhookCallModel = $properties['webhook_call_model'];
    }
}
