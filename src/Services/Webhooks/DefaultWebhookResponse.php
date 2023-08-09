<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Webhooks\RespondsToWebhook;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultWebhookResponse implements RespondsToWebhook
{
    public function respondToValidWebhook(Request $request, WebhookConfig $config): Response
    {
        return response()->json(['status' => 'success']);
    }
}
