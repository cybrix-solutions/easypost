<?php

declare(strict_types=1);

namespace EasyPost\Test\Mocking;

final class MockRequestResponseInfo
{
    public function __construct(
        public readonly int $statusCode,
        public readonly string $body,
    ) {}
}
