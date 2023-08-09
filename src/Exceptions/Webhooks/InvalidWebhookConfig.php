<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Webhooks\RespondsToWebhook;
use CybrixSolutions\EasyPost\Contracts\Webhooks\WebhookProfile;
use Exception;

final class InvalidWebhookConfig extends Exception
{
    public static function invalidWebhookProfile(string $webhookProfile): self
    {
        $contract = WebhookProfile::class;

        return new self("`{$webhookProfile}` is not a valid webhook profile class. A valid webhook profile is a class that implements `{$contract}`.");
    }

    public static function invalidWebhookResponse(string $webhookResponse): self
    {
        $contract = RespondsToWebhook::class;

        return new self("`{$webhookResponse}` is not a valid webhook response class. A valid webhook response is a class that implements `{$contract}`.");
    }

    public static function signingSecretNotSet(): self
    {
        return new self('The webhook signing secret is not set. Make sure that the `webhook_secret` config key is set to the correct value.');
    }

    public static function invalidPrunable(mixed $value): self
    {
        return new self("`{$value}` is not a valid amount of days.");
    }
}
