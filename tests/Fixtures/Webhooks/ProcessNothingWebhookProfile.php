<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Webhooks\WebhookProfile;
use Illuminate\Http\Request;

final class ProcessNothingWebhookProfile implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        return false;
    }
}
