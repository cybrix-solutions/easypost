<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Events\Webhooks;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;

final class InvalidWebhookSignatureEvent
{
    use Dispatchable;
    use InteractsWithSockets;

    public function __construct(public Request $request)
    {
    }
}
