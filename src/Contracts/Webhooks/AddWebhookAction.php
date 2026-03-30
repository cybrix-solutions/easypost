<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Contracts\Webhooks;

use CybrixSolutions\EasyPost\Dto\EasyPostWebhook;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookCreationFailed;

interface AddWebhookAction
{
    /**
     * @throws WebhookCreationFailed
     */
    public function __invoke(string $url, bool $testMode): EasyPostWebhook;
}
