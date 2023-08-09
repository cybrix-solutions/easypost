<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Exceptions\Webhooks;

use Exception;

final class InvalidWebhookSignature extends Exception
{
    public static function make(): self
    {
        return new self('The signature is invalid.');
    }
}
