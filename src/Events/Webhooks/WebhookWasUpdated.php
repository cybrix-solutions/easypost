<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Events\Webhooks;

use CybrixSolutions\EasyPost\Dto\EasyPostWebhook;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class WebhookWasUpdated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public EasyPostWebhook $webhook) {}
}
