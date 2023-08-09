<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Webhooks\RespondsToWebhook;
use CybrixSolutions\EasyPost\Services\Webhooks\WebhookConfig;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CustomRespondsToWebhook implements RespondsToWebhook
{
    public function respondToValidWebhook(Request $request, WebhookConfig $config): Response
    {
        return response()->json(['foo' => 'bar']);
    }
}
