<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Http\Livewire\Concerns\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Webhooks\AddWebhookAction;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookCreationFailed;
use CybrixSolutions\EasyPost\Facades\EasyPost;
use Illuminate\Support\Str;

/**
 * @mixin \Livewire\Component
 */
trait AddsWebhooks
{
    public bool $showAddForm = false;

    public ?string $addError = null;

    public array $webhookState = [
        'url' => '',
    ];

    abstract protected function authorizeWebhookAdd(bool $testMode): void;

    public function addWebhook(): void
    {
        $this->showAddForm = true;
        $this->reset('addError');
        $this->webhookState['url'] = $this->defaultTestWebhookUrl();
    }

    public function storeWebhook(AddWebhookAction $action): void
    {
        $this->authorizeWebhookAdd(testMode: true);

        $this->resetErrorBag();

        try {
            $webhook = $action(url: $this->webhookState['url'], testMode: true);
        } catch (WebhookCreationFailed $e) {
            $this->addError('url', $e->getMessage());

            return;
        }

        $this->reset('showAddForm', 'webhookState', 'addError');
        $this->onAddWebhook(testMode: true);
        $this->emit('webhook.created', $webhook->id);
    }

    public function configureProductionWebhook(AddWebhookAction $action): void
    {
        $this->authorizeWebhookAdd(testMode: false);

        try {
            $webhook = $action(url: EasyPost::productionWebhookUrl(), testMode: false);
        } catch (WebhookCreationFailed $e) {
            $this->addError = $e->getMessage();

            return;
        }

        $this->reset('addError');
        $this->onAddWebhook(testMode: false);
        $this->emit('webhook.created', $webhook->id);
    }

    public function hydrateAddsWebhooks(): void
    {
        $this->listeners['webhook.add'] = 'addWebhook';
    }

    protected function defaultTestWebhookUrl(): string
    {
        $domain = rtrim(request()->getSchemeAndHttpHost() ?? config('app.url'), '/');
        $path = ltrim(config('easypost.webhook_url'), '/');

        return Str::of("{$domain}/{$path}")
            ->lower()
            ->toString();
    }

    protected function onAddWebhook(bool $testMode): void
    {
    }
}
