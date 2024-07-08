<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Webhooks\DeleteWebhookAction as DeleteWebhookActionContract;
use CybrixSolutions\EasyPost\Events\Webhooks\WebhookWasDeleted;
use CybrixSolutions\EasyPost\Services\WebhooksService;

class DeleteWebhookAction implements DeleteWebhookActionContract
{
    public function __construct(protected WebhooksService $api) {}

    public function __invoke(string $webhookId, bool $testMode): void
    {
        $this->api->delete(
            id: $webhookId,
            testMode: $testMode,
        );

        WebhookWasDeleted::dispatch($webhookId, $testMode);
    }
}
