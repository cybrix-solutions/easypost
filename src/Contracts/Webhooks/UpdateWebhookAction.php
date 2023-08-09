<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Webhooks;

use CybrixSolutions\EasyPost\Dto\EasyPostWebhook;

interface UpdateWebhookAction
{
    /**
     * @throws \CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookUpdateFailed
     */
    public function __invoke(string $webhookId, ?string $webhookSecret, bool $testMode = false): EasyPostWebhook;
}
