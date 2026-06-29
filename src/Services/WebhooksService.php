<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Services;

use CybrixSolutions\EasyPost\Dto\EasyPostWebhook;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookCreationFailed;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookDeletionFailed;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookRetrievalFailed;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookUpdateFailed;
use CybrixSolutions\EasyPost\Facades\EasyPost;
use CybrixSolutions\EasyPost\Services\Api\EasyPostClient;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\EasyPostMock;
use EasyPost\EasyPostClient as ExternalEasyPostClient;
use EasyPost\Exception\Api\ApiException;
use EasyPost\Exception\Api\NotFoundException;
use EasyPost\Test\Mocking\MockingUtility;
use EasyPost\Util\InternalUtil as EasyPostUtil;
use EasyPost\Webhook;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

use function CybrixSolutions\EasyPost\easypostObjectToArray;

final class WebhooksService extends EasyPostClient
{
    private array $pendingProductionMocks = [];

    private array $pendingTestMocks = [];

    public function __construct(
        private readonly string $productionApiKey,
        private readonly string $testApiKey,
        private readonly string $webhookSecret,
        private readonly string $webhookUrlPath,
    ) {
        parent::__construct($productionApiKey);
    }

    /**
     * @return Collection<int, EasyPostWebhook>
     */
    public function all(): Collection
    {
        $webhooks = $this->allFromApi(testMode: false);

        if ($this->testApiKey || $this->pendingTestMocks !== []) {
            $testWebhooks = $this->allFromApi(testMode: true);

            $webhooks = $webhooks->concat($testWebhooks->toArray());
        }

        return $webhooks;
    }

    public function addProductionWebhook(?string $url = null): Webhook
    {
        $url ??= EasyPost::productionWebhookUrl();

        try {
            return $this->api(testMode: false)->webhook->create([
                'url' => $url,
                'webhook_secret' => $this->webhookSecret,
            ]);
        } catch (ApiException $e) {
            throw WebhookCreationFailed::because($e->getMessage());
        }
    }

    public function addTestWebhook(?string $url = null): Webhook
    {
        $url ??= config('app.url');

        try {
            return $this->api(testMode: true)->webhook->create([
                'url' => $url,
                'webhook_secret' => $this->webhookSecret,
            ]);
        } catch (ApiException $e) {
            throw WebhookCreationFailed::because($e->getMessage());
        }
    }

    public function find(string $id, bool $testMode = false): Webhook
    {
        try {
            return $this->api(testMode: $testMode)->webhook->retrieve($id);
        } catch (NotFoundException $e) {
            throw WebhookRetrievalFailed::notFound($e->getMessage());
        } catch (ApiException $e) {
            throw WebhookRetrievalFailed::generalError($e->getMessage());
        }
    }

    public function delete(string $id, bool $testMode = false): bool
    {
        try {
            $webhook = $this->find($id, $testMode);

            $this->api(testMode: $testMode)->webhook->delete($webhook->id);
        } catch (WebhookRetrievalFailed|ApiException $e) {
            throw WebhookDeletionFailed::because($e->getMessage());
        }

        cache()->forget($this->cacheKeyFor($testMode));

        return true;
    }

    public function update(string $id, ?string $webhookSecret, bool $testMode = false): Webhook
    {
        try {
            $webhook = $this->find($id, $testMode);

            return $this->api(testMode: $testMode)->webhook->update($webhook->id, [
                'webhook_secret' => $webhookSecret,
            ]);
        } catch (WebhookRetrievalFailed|ApiException $e) {
            throw WebhookUpdateFailed::because($e->getMessage());
        }
    }

    // Testing Utils...
    public function addProductionMock(EasyPostMock $mock): self
    {
        $this->ensureMockingUtilitiesAreLoaded();

        $this->pendingProductionMocks[] = $mock->asMockRequest();

        return $this;
    }

    public function addTestMock(EasyPostMock $mock): self
    {
        $this->ensureMockingUtilitiesAreLoaded();

        $this->pendingTestMocks[] = $mock->asMockRequest();

        return $this;
    }

    public function resetMocks(): void
    {
        $this->pendingProductionMocks = [];
        $this->pendingTestMocks = [];
    }

    private function api(bool $testMode): ExternalEasyPostClient
    {
        if ($testMode === false && count($this->pendingProductionMocks)) {
            return $this->mockedProductionApi();
        }

        if ($testMode === true && count($this->pendingTestMocks)) {
            return $this->mockedTestApi();
        }

        $this->setApiKey($testMode ? $this->testApiKey : $this->productionApiKey);

        return $this->client;
    }

    private function webhookPath(): string
    {
        return Str::of($this->webhookUrlPath)
            ->ltrim('/')
            ->lower()
            ->toString();
    }

    /**
     * @return Collection<int, EasyPostWebhook>
     */
    private function allFromApi(bool $testMode): Collection
    {
        $webhooks = cache()->remember(
            key: $this->cacheKeyFor($testMode),
            ttl: $this->cacheTtlFor($testMode),
            callback: function () use ($testMode) {
                $webhooks = rescue(fn () => $this->api(testMode: $testMode)->webhook->all());

                return Arr::map($webhooks?->webhooks ?? [], fn (Webhook $webhook) => easypostObjectToArray($webhook));
            },
        );

        return collect(EasyPostUtil::convertToEasyPostObject($this->client, $webhooks ?? []))
            ->map(fn (Webhook $webhook) => new EasyPostWebhook($webhook));
    }

    private function cacheKeyFor(bool $testMode): ?string
    {
        return $testMode
            ? config('easypost.cache.test_webhooks.key')
            : config('easypost.cache.production_webhooks.key');
    }

    private function cacheTtlFor(bool $testMode): mixed
    {
        return $testMode
            ? config('easypost.cache.test_webhooks.ttl')
            : config('easypost.cache.production_webhooks.ttl');
    }

    private function mockedProductionApi(): ExternalEasyPostClient
    {
        return new ExternalEasyPostClient(
            apiKey: $this->productionApiKey,
            mockingUtility: new MockingUtility(...$this->pendingProductionMocks),
        );
    }

    private function mockedTestApi(): ExternalEasyPostClient
    {
        return new ExternalEasyPostClient(
            apiKey: $this->testApiKey,
            mockingUtility: new MockingUtility(...$this->pendingTestMocks),
        );
    }
}
