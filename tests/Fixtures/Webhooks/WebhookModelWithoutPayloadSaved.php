<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Webhooks;

use CybrixSolutions\EasyPost\Models\WebhookCall;
use CybrixSolutions\EasyPost\Services\Webhooks\WebhookConfig;
use Illuminate\Http\Request;

final class WebhookModelWithoutPayloadSaved extends WebhookCall
{
    public static function storeWebhook(WebhookConfig $config, Request $request): self
    {
        return self::create([
            'name' => $request->input('description'),
            'url' => 'https://example.com',
            'payload' => [],
        ]);
    }
}
