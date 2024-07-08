<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Actions\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Webhooks\AddWebhookAction as AddWebhookActionContract;
use CybrixSolutions\EasyPost\Dto\EasyPostWebhook;
use CybrixSolutions\EasyPost\Events\Webhooks\WebhookWasCreated;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use EasyPost\Webhook;
use Illuminate\Support\Facades\Validator;

class AddWebhookAction implements AddWebhookActionContract
{
    public function __construct(protected WebhooksService $api) {}

    public function __invoke(string $url, bool $testMode): EasyPostWebhook
    {
        $this->validate($url);

        $webhook = new EasyPostWebhook($this->createWebhook($url, $testMode));

        cache()->forget($this->webhookCacheKey($testMode));

        WebhookWasCreated::dispatch($webhook);

        return $webhook;
    }

    protected function createWebhook(string $url, bool $testMode): Webhook
    {
        if ($testMode) {
            return $this->api->addTestWebhook($url);
        }

        return $this->api->addProductionWebhook($url);
    }

    protected function validate(string $url): void
    {
        Validator::make(data: ['url' => $url], rules: [
            'url' => ['required', 'string'],
        ])->validate();
    }

    protected function webhookCacheKey(bool $testMode): string
    {
        return $testMode
            ? config('easypost.cache.test_webhooks.key')
            : config('easypost.cache.production_webhooks.key');
    }
}
