<?php

declare(strict_types=1);

namespace EasyPost\Test\Mocking;

final class MockRequest
{
    public function __construct(
        public readonly MockRequestMatchRule $matchRule,
        public readonly MockRequestResponseInfo $responseInfo,
    ) {}

    public function matches(string $method, string $url): bool
    {
        return $this->matchRule->matches($method, $url);
    }
}
