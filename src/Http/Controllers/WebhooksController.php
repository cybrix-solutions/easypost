<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Http\Controllers;

use CybrixSolutions\EasyPost\Services\Webhooks\WebhookConfig;
use CybrixSolutions\EasyPost\Services\Webhooks\WebhookProcessor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebhooksController
{
    public function __invoke(Request $request, WebhookConfig $config): Response
    {
        return (new WebhookProcessor($request, $config))->process();
    }
}
