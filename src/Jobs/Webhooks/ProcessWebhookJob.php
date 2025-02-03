<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Jobs\Webhooks;

use CybrixSolutions\EasyPost\Models\WebhookCall;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public WebhookCall $webhookCall) {}

    public function handle(): void
    {
        $jobClass = $this->jobClass($this->webhookCall->payload);
        if (! class_exists($jobClass ?? '')) {
            return;
        }

        $jobClass::dispatch($this->webhookCall);
    }

    protected function jobClass(array $payload): ?string
    {
        $jobKey = $payload['description'] ?? null;

        if (! $jobKey) {
            return null;
        }

        return config("easypost.webhook_config.processors.{$jobKey}");
    }
}
