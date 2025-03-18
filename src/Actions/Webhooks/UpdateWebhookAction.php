<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Webhooks\UpdateWebhookAction as UpdateWebhookActionContract;
use CybrixSolutions\EasyPost\Dto\EasyPostWebhook;
use CybrixSolutions\EasyPost\Events\Webhooks\WebhookWasUpdated;
use CybrixSolutions\EasyPost\Services\WebhooksService;

class UpdateWebhookAction implements UpdateWebhookActionContract
{
    public function __construct(protected WebhooksService $api) {}

    public function __invoke(string $webhookId, ?string $webhookSecret, bool $testMode = false): EasyPostWebhook
    {
        $webhookSecret ??= config('easypost.webhook_secret');

        $webhook = $this->api->update(
            id: $webhookId,
            webhookSecret: $webhookSecret,
            testMode: $testMode,
        );

        $easypostWebhook = new EasyPostWebhook($webhook);

        WebhookWasUpdated::dispatch($easypostWebhook);

        cache()->forget($this->webhookCacheKey($testMode));

        return $easypostWebhook;
    }

    protected function webhookCacheKey(bool $testMode): string
    {
        return $testMode
            ? config('easypost.cache.test_webhooks.key')
            : config('easypost.cache.production_webhooks.key');
    }
}
