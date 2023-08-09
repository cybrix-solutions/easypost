<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Webhooks\WebhookProfile;
use Illuminate\Http\Request;

class DefaultWebhookProfile implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        return array_key_exists(
            $request->input()['description'] ?? '',
            config('easypost.webhook_config.processors', []),
        );
    }
}
