<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Http\Livewire\Concerns\Webhooks;

use CybrixSolutions\EasyPost\Dto\EasyPostWebhook;
use CybrixSolutions\EasyPost\Facades\EasyPost;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use Illuminate\Support\Collection;

/**
 * @property-read \Illuminate\Support\Collection<int, \CybrixSolutions\EasyPost\Dto\EasyPostWebhook> $webhooks
 *
 * @mixin \Livewire\Component
 */
trait ListsWebhooks
{
    public function getWebhooksProperty(): Collection
    {
        return app(WebhooksService::class)->all();
    }

    public function hasProductionWebhook(): bool
    {
        return $this->webhooks->filter(function (EasyPostWebhook $webhook) {
            return $webhook->mode === 'production'
                && strtolower($webhook->url) === EasyPost::productionWebhookUrl();
        })->isNotEmpty();
    }

    public function hydrateListsWebhooks(): void
    {
        $this->listeners['webhook.created'] = '$refresh';
        $this->listeners['webhook.deleted'] = '$refresh';
    }
}
