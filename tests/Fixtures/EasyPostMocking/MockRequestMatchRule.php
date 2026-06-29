<?php

declare(strict_types=1);

namespace EasyPost\Test\Mocking;

final class MockRequestMatchRule
{
    public function __construct(
        public readonly string $method,
        public readonly string $urlPattern,
    ) {}

    public function matches(string $method, string $url): bool
    {
        return strtolower($this->method) === strtolower($method)
            && preg_match($this->urlPattern, $url) === 1;
    }
}
