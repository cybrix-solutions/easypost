<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Webhooks;

use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\EasyPostMock;

final class WebhookMock extends EasyPostMock
{
    protected string $urlPattern = '/v2\\/webhooks\\/\\S*$/';

    private string $id = 'hook_123456';

    private string $mode = 'production';

    private string $url = 'https://example.com/webhook';

    private ?string $disabledAt = null;

    public function urlPattern(): string
    {
        return $this->method === 'post'
            ? '/v2\\/webhooks$/'
            : $this->urlPattern;
    }

    public function withId(string $id): self
    {
        if ($this->method !== 'post') {
            $this->urlPattern = '/v2\\/webhooks\\/' . $id . '$/';
        }

        $this->id = $id;

        return $this;
    }

    public function withMode(string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function withUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function withDisabledAt(?string $disabledAt): self
    {
        $this->disabledAt = $disabledAt;

        return $this;
    }

    protected function getPayload(): array
    {
        return [
            'id' => $this->id,
            'object' => 'Webhook',
            'mode' => $this->mode,
            'url' => $this->url,
            'created_at' => '2023-01-01T20:04:19Z',
            'disabled_at' => $this->disabledAt,
        ];
    }
}
