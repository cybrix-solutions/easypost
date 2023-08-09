<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Dto\EasyPostWebhook;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookCreationFailed;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookDeletionFailed;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookRetrievalFailed;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookUpdateFailed;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Webhooks\WebhookListMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Webhooks\WebhookMock;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->service = app(WebhooksService::class);

    config()->set('easypost.webhook_url', '/api/webhooks/easypost');
});

afterEach(function () {
    app(WebhooksService::class)->resetMocks();
});

it('can list all webhooks for an account', function () {
    mockWebhookApi([
        WebhookListMock::make()->productionOnly(),
    ], [
        WebhookListMock::make()->testOnly(),
    ]);

    $webhooks = $this->service->all();

    expect($webhooks)->toHaveCount(2)
        ->and($webhooks)->toContainOnlyInstancesOf(EasyPostWebhook::class)
        ->and($webhooks[0]->id)->toBe('hook_prod')
        ->and($webhooks[0]->mode)->toBe('production')
        ->and($webhooks[1]->id)->toBe('hook_test')
        ->and($webhooks[1]->mode)->toBe('test');
});

it('can retrieve a webhook', function () {
    mockWebhookApi([
        WebhookMock::make()->withId('hook_my_hook'),
    ]);

    $webhook = $this->service->find(id: 'hook_my_hook', testMode: false);

    expect($webhook->id)->toBe('hook_my_hook')
        ->and($webhook->mode)->toBe('production');
});

it('throws an exception when trying to find a webhook that does not exist', function () {
    mockWebhookApi([
        WebhookMock::notFound(),
    ]);

    $this->service->find(id: 'fake-id', testMode: false);
})->throws(WebhookRetrievalFailed::class, 'The requested resource could not be found.');

it('throws an exception when trying to retrieve a webhook for the wrong environment', function () {
    mockWebhookApi([
        WebhookMock::make()->withId('hook_123456'),
    ]);

    $webhook = $this->service->find(id: 'hook_123456', testMode: false);

    expect($webhook->id)->toBe('hook_123456')
        ->and($webhook->mode)->toBe('production');

    mockWebhookApi([
        WebhookMock::make()->withId('hook_123456'),
    ], [
        WebhookMock::notFound(),
    ]);

    $this->service->find(id: 'hook_123456', testMode: true);
})->throws(WebhookRetrievalFailed::class, 'The requested resource could not be found.');

it('can create a production webhook', function () {
    mockWebhookApi([
        WebhookMock::make()
            ->usingMethod('post')
            ->withUrl('https://my-domain.test/api/webhooks/easypost')
            ->withId('my_hook'),
    ]);

    $webhook = $this->service->addProductionWebhook('https://my-domain.test/api/webhooks/easypost');

    expect($webhook->id)->toBe('my_hook')
        ->and($webhook->mode)->toBe('production')
        ->and($webhook->url)->toBe('https://my-domain.test/api/webhooks/easypost');
});

it('can create a test webhook', function () {
    mockWebhookApi(testMocks: [
        WebhookMock::make()
            ->usingMethod('post')
            ->withUrl('https://my-domain.test/api/webhooks/easypost')
            ->withMode('test')
            ->withId('my_hook'),
    ]);

    $webhook = $this->service->addTestWebhook('https://my-domain.test/api/webhooks/easypost');

    expect($webhook->id)->toBe('my_hook')
        ->and($webhook->mode)->toBe('test')
        ->and($webhook->url)->toBe('https://my-domain.test/api/webhooks/easypost');
});

it('uses the app url by default when creating a webhook', function () {
    config(['app.url' => 'https://example.com']);

    mockWebhookApi([
        WebhookMock::make()
            ->usingMethod('post')
            ->withUrl('https://example.com/api/webhooks/easypost')
            ->withId('my_hook'),
    ]);

    $webhook = $this->service->addProductionWebhook();

    expect($webhook->id)->toBe('my_hook')
        ->and($webhook->mode)->toBe('production')
        ->and($webhook->url)->toBe('https://example.com/api/webhooks/easypost');
});

it('throws an exception for bad requests when creating a webhook', function () {
    mockWebhookApi([
        WebhookMock::badRequest()->usingMethod('post'),
    ]);

    $this->service->addProductionWebhook('https://my-domain.test');
})->throws(WebhookCreationFailed::class, 'Malformed request');

it('can update a webhook', function () {
    // when a webhook is updated, the API will automatically re-enable it if it was disabled.
    mockWebhookApi([
        WebhookMock::make()
            ->withId('my_hook')
            ->withDisabledAt('2021-01-01'),
        WebhookMock::make()
            ->usingMethod('patch')
            ->withId('my_hook')
            ->withDisabledAt(null),
    ]);

    $webhook = $this->service->find(id: 'my_hook', testMode: false);

    expect($webhook->disabled_at)->toBe('2021-01-01');

    $webhook = $this->service->update(id: 'my_hook', webhookSecret: 'new_secret', testMode: false);

    expect($webhook->disabled_at)->toBeNull();
});

it('throws an exception if a webhook is not found when trying to update it', function () {
    mockWebhookApi([
        WebhookMock::notFound(),
    ]);

    $this->service->update(id: 'my_hook', webhookSecret: 'new_secret', testMode: false);
})->throws(WebhookUpdateFailed::class, 'The requested resource could not be found.');

it('throws an exception for general api errors when trying to update a webhook', function () {
    mockWebhookApi([
        WebhookMock::make()->withId('my_hook'),
        WebhookMock::badRequest()->usingMethod('patch'),
    ]);

    $this->service->update(id: 'my_hook', webhookSecret: 'new_secret', testMode: false);
})->throws(WebhookUpdateFailed::class, 'Malformed request');

it('can delete a webhook', function () {
    Cache::spy();

    config()->set('easypost.cache.production_webhooks.key', 'easypost::production_webhooks');

    mockWebhookApi([
        WebhookMock::make()->withId('my_hook'),
        WebhookMock::make()->usingMethod('delete')->withId('my_hook'),
    ]);

    $result = $this->service->delete(id: 'my_hook', testMode: false);

    expect($result)->toBeTrue();

    Cache::shouldHaveReceived('forget')->with('easypost::production_webhooks');
});

it('throws an exception when trying to delete a webhook that does not exist', function () {
    mockWebhookApi([
        WebhookMock::notFound(),
    ]);

    $this->service->delete(id: 'my_hook', testMode: false);
})->throws(WebhookDeletionFailed::class, 'The requested resource could not be found.');

it('does not attempt to clear the cache if webhook deletion fails', function () {
    mockWebhookApi([
        WebhookMock::notFound(),
    ]);

    Cache::spy();

    try {
        $this->service->delete(id: 'my_hook', testMode: false);
    } catch (WebhookDeletionFailed) {
    }

    Cache::shouldNotHaveReceived('forget');
});

it('throws an exception for general api errors when trying to delete a webhook', function () {
    mockWebhookApi([
        WebhookMock::make()->withId('my_hook'),
        WebhookMock::badRequest()->usingMethod('delete'),
    ]);

    $this->service->delete(id: 'my_hook', testMode: false);
})->throws(WebhookDeletionFailed::class, 'Malformed request');

it('can get the configured webhook url path for an application', function (string $path, string $expectedPath) {
    $service = new WebhooksService(
        'production_key',
        'test_key',
        'secret',
        $path,
    );

    expect(invade($service)->webhookPath())->toBe($expectedPath);
})->with([
    ['foo/bar', 'foo/bar'],
    ['/api/webhooks/easypost', 'api/webhooks/easypost'],
    ['MY_endpoint/', 'my_endpoint/'],
]);

it('can get the configured cache key for each environment', function () {
    config()->set('easypost.cache.production_webhooks.key', 'easypost::production_webhooks');
    config()->set('easypost.cache.test_webhooks.key', 'easypost::test_webhooks');

    expect(invade($this->service)->cacheKeyFor(testMode: false))->toBe('easypost::production_webhooks')
        ->and(invade($this->service)->cacheKeyFor(testMode: true))->toBe('easypost::test_webhooks');
});

it('can get the cache ttl for each environment', function () {
    config()->set('easypost.cache.production_webhooks.ttl', 60);
    config()->set('easypost.cache.test_webhooks.ttl', 120);

    expect(invade($this->service)->cacheTtlFor(testMode: false))->toBe(60)
        ->and(invade($this->service)->cacheTtlFor(testMode: true))->toBe(120);
});
