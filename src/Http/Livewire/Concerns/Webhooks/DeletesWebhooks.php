<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Http\Livewire\Concerns\Webhooks;

use CybrixSolutions\EasyPost\Contracts\Webhooks\DeleteWebhookAction;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookDeletionFailed;

/**
 * @mixin \Livewire\Component
 */
trait DeletesWebhooks
{
    public bool $showDelete = false;

    public ?string $deleting = null;

    public ?string $deleteMode = null;

    public ?string $deleteError = null;

    abstract protected function authorizeWebhookDelete(): void;

    public function confirmDelete(string $webhookId, string $mode): void
    {
        $this->deleting = $webhookId;
        $this->deleteMode = $mode;
        $this->showDelete = true;
        $this->reset('deleteError');
    }

    public function deleteWebhook(DeleteWebhookAction $deleter): void
    {
        if (! $this->deleting || ! $this->deleteMode) {
            return;
        }

        $this->authorizeWebhookDelete();

        try {
            $deleter($this->deleting, $this->deleteMode === 'test');
        } catch (WebhookDeletionFailed $e) {
            $this->deleteError = $e->getMessage();

            return;
        }

        $this->emit('webhook.deleted', $this->deleting, $this->deleteMode);
        $this->reset('showDelete', 'deleting', 'deleteMode');

        $this->onWebhookDelete();
    }

    public function hydrateDeletesWebhooks(): void
    {
        $this->listeners['webhook.confirm_delete'] = 'confirmDelete';
    }

    protected function onWebhookDelete(): void {}
}
