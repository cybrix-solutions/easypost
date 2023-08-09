<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Webhooks;

use Exception;

final class WebhookCreationFailed extends Exception
{
    public static function because(string $reason): self
    {
        return new self(__('easypost::exceptions.webhook_creation_api_fail', ['message' => $reason]));
    }
}
