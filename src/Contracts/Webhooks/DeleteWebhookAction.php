<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Webhooks;

use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookDeletionFailed;

interface DeleteWebhookAction
{
    /**
     * @throws WebhookDeletionFailed
     */
    public function __invoke(string $webhookId, bool $testMode): void;
}
