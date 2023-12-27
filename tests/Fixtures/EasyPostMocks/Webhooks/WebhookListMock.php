<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Webhooks;

use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\EasyPostMock;

final class WebhookListMock extends EasyPostMock
{
    protected string $urlPattern = '/v2\\/webhooks/';

    private array $types = ['production', 'test'];

    public function productionOnly(): self
    {
        $this->types = ['production'];

        return $this;
    }

    public function testOnly(): self
    {
        $this->types = ['test'];

        return $this;
    }

    protected function getPayload(): array
    {
        $webhooks = [];

        foreach ($this->types as $type) {
            match ($type) {
                'production' => $webhooks[] = $this->productionWebhook(),
                'test' => $webhooks[] = $this->testWebhook(),
            };
        }

        return [
            'webhooks' => $webhooks,
        ];
    }

    protected function productionWebhook(): array
    {
        return [
            'id' => 'hook_prod',
            'object' => 'Webhook',
            'mode' => 'production',
            'url' => 'https://example.com/webhook',
            'created_at' => '2023-01-01T20:04:19Z',
            'disabled_at' => null,
        ];
    }

    protected function testWebhook(): array
    {
        return [
            'id' => 'hook_test',
            'object' => 'Webhook',
            'mode' => 'test',
            'url' => 'https://example.com/webhook',
            'created_at' => '2023-01-01T20:04:19Z',
            'disabled_at' => null,
        ];
    }
}
