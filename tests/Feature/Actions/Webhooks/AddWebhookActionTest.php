<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\Webhooks\AddWebhookAction;
use CybrixSolutions\EasyPost\Contracts\Webhooks\AddWebhookAction as AddWebhookActionContract;
use CybrixSolutions\EasyPost\Events\Webhooks\WebhookWasCreated;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Webhooks\WebhookMock;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    config()->set('easypost.actions.add_webhook', AddWebhookAction::class);
    config()->set('easypost.cache.test_webhooks.key', 'test_webhooks');
    config()->set('easypost.cache.production_webhooks.key', 'production_webhooks');

    Event::fake();

    mockWebhookApi([
        WebhookMock::make()
            ->usingMethod('post')
            ->withId('my_hook')
            ->withUrl('https://example.com/webhooks/easypost'),
    ]);
});

afterEach(function () {
    app(WebhooksService::class)->resetMocks();
});

it('creates a new production webhook', function () {
    Cache::spy();

    app(AddWebhookActionContract::class)(url: 'https://example.com', testMode: false);

    Cache::shouldHaveReceived('forget')->with('production_webhooks');

    Event::assertDispatched(function (WebhookWasCreated $event) {
        return $event->webhook->mode === 'production'
            && $event->webhook->id === 'my_hook'
            && $event->webhook->url === 'https://example.com/webhooks/easypost';
    });
});

it('creates a new test webhook', function () {
    Cache::spy();

    mockWebhookApi(testMocks: [
        WebhookMock::make()
            ->usingMethod('post')
            ->withId('my_test_hook')
            ->withMode('test')
            ->withUrl('https://test.example.com/webhooks/easypost'),
    ]);

    app(AddWebhookActionContract::class)(url: 'https://test.example.com/webhooks/easypost', testMode: true);

    Cache::shouldHaveReceived('forget')->with('test_webhooks');

    Event::assertDispatched(function (WebhookWasCreated $event) {
        return $event->webhook->mode === 'test'
            && $event->webhook->id === 'my_test_hook'
            && $event->webhook->url === 'https://test.example.com/webhooks/easypost';
    });
});

it('requires a url', function () {
    app(AddWebhookActionContract::class)(url: '', testMode: false);
})->throws(ValidationException::class);

it('does not reset the cache or dispatch events if validation fails', function () {
    Cache::spy();

    try {
        app(AddWebhookActionContract::class)(url: '', testMode: false);
    } catch (ValidationException) {
    }

    Cache::shouldNotHaveReceived('forget');
    Event::assertNotDispatched(WebhookWasCreated::class);
});
