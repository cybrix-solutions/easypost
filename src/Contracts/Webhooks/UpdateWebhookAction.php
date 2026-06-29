<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Webhooks;

use CybrixSolutions\EasyPost\Dto\EasyPostWebhook;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookUpdateFailed;

interface UpdateWebhookAction
{
    /**
     * @throws WebhookUpdateFailed
     */
    public function __invoke(string $webhookId, ?string $webhookSecret, bool $testMode = false): EasyPostWebhook;
}
