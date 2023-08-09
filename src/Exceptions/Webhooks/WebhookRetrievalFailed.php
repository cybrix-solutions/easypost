<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Webhooks;

use Exception;

final class WebhookRetrievalFailed extends Exception
{
    public static function notFound(string $message): self
    {
        return new self($message);
    }

    public static function generalError(string $message): self
    {
        return new self(__('easypost::exceptions.webhook_retrieval_fail', ['message' => $message]));
    }
}
