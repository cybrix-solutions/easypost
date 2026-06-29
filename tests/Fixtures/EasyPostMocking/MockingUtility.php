<?php

declare(strict_types=1);

namespace EasyPost\Test\Mocking;

final class MockingUtility
{
    /**
     * @var array<int, MockRequest>
     */
    private array $requests;

    public function __construct(MockRequest ...$requests)
    {
        $this->requests = $requests;
    }

    public function findMatchingMockRequest(string $method, string $url): ?MockRequest
    {
        foreach ($this->requests as $request) {
            if ($request->matches($method, $url)) {
                return $request;
            }
        }

        return null;
    }
}
