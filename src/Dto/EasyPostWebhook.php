<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Dto;

use EasyPost\Webhook;

/**
 * This DTO object wrapper is necessary since for some reason if the webhook is passed directly to a view component,
 * we lose all its properties...
 *
 * @property-read string $id+
 * @property-read string $mode
 * @property-read string $url
 * @property-read string $created_at
 * @property-read null|string $disabled_at
 */
final class EasyPostWebhook
{
    public function __construct(public Webhook $webhook) {}

    public function __get(string $name): mixed
    {
        return $this->webhook->{$name};
    }

    public function isActive(): bool
    {
        return $this->webhook->disabled_at === null;
    }
}
