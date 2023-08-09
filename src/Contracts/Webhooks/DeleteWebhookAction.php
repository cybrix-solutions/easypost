<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Webhooks;

interface DeleteWebhookAction
{
    /**
     * @throws \CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookDeletionFailed
     */
    public function __invoke(string $webhookId, bool $testMode): void;
}
