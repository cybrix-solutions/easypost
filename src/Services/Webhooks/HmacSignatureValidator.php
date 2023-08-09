<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services\Webhooks;

use CybrixSolutions\EasyPost\Exceptions\Webhooks\InvalidWebhookConfig;
use Illuminate\Http\Request;

class HmacSignatureValidator
{
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $signature = $request->header($config->signatureHeaderName);

        if (! $signature) {
            return false;
        }

        $signingSecret = $config->signingSecret;

        if (blank($signingSecret)) {
            throw InvalidWebhookConfig::signingSecretNotSet();
        }

        $computedSignature = hash_hmac('sha256', $request->getContent(), $signingSecret);

        // EasyPost prefixes the signature with hmac-sha256-hex=
        $digest = "hmac-sha256-hex={$computedSignature}";

        return hash_equals($digest, $signature);
    }
}
