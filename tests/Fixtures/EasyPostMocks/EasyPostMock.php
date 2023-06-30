<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks;

abstract class EasyPostMock
{
    protected int $statusCode = 200;

    protected string $method = 'get';

    protected string $urlPattern = '';

    abstract protected function getPayload(): array;

    public function payload(): array
    {
        if ($this->statusCode === 404) {
            return $this->notFoundPayload();
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

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function usingMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function usingStatusCode(int $statusCode): self
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

    protected function notFoundPayload(): array
    {
        return [
            'error' => [
                'code' => 404,
                'message' => 'The requested resource could not be found.',
            ],
        ];
    }
}
