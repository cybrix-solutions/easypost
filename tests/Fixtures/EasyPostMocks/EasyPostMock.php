<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks;

use EasyPost\Test\Mocking\MockRequest;
use EasyPost\Test\Mocking\MockRequestMatchRule;
use EasyPost\Test\Mocking\MockRequestResponseInfo;

abstract class EasyPostMock
{
    protected int|string $statusCode = 200;

    protected string $method = 'get';

    protected string $urlPattern = '';

    abstract protected function getPayload(): array;

    public function asMockRequest(): MockRequest
    {
        return new MockRequest(
            new MockRequestMatchRule(
                $this->method(),
                $this->urlPattern(),
            ),
            new MockRequestResponseInfo(
                $this->statusCode(),
                json_encode($this->payload()),
            ),
        );
    }

    public function payload(): array
    {
        if ($this->statusCode === 404) {
            return $this->notFoundPayload();
        }

        if ($this->statusCode === 'BAD_REQUEST') {
            return $this->badRequestPayload();
        }

        return $this->getPayload();
    }

    public function urlPattern(): string
    {
        return $this->urlPattern;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function statusCode(): int|string
    {
        if (is_string($this->statusCode)) {
            return [
                'BAD_REQUEST' => 422,
                'ADDRESS.VERIFY.FAILURE' => 422,
            ][$this->statusCode];
        }

        return $this->statusCode;
    }

    public function usingMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function usingStatusCode(int|string $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public static function make(): self
    {
        return new static;
    }

    public static function notFound(): self
    {
        return static::make()->usingStatusCode(404);
    }

    public static function badRequest(): self
    {
        return static::make()->usingStatusCode('BAD_REQUEST');
    }

    protected function notFoundPayload(): array
    {
        return [
            'error' => [
                'code' => 404,
                'message' => 'The requested resource could not be found.',
            ],
        ];
    }

    protected function badRequestPayload(): array
    {
        return [
            'error' => [
                'code' => 'BAD_REQUEST',
                'message' => 'Malformed request. Please check the contents and retry.',
                'errors' => [],
            ],
        ];
    }
}
