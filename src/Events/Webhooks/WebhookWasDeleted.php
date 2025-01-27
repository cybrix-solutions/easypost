<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Events\Webhooks;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class WebhookWasDeleted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public string $webhookId, public bool $testMode) {}
}
