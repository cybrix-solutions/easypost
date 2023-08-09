<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Webhooks;

use CybrixSolutions\EasyPost\Services\Webhooks\WebhookConfig;
use Illuminate\Http\Request;

interface RespondsToWebhook
{
    public function respondToValidWebhook(Request $request, WebhookConfig $config);
}
