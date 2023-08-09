<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\Webhooks\DeleteWebhookAction;
use CybrixSolutions\EasyPost\Contracts\Webhooks\DeleteWebhookAction as DeleteWebhookActionContract;
use CybrixSolutions\EasyPost\Events\Webhooks\WebhookWasDeleted;
use CybrixSolutions\EasyPost\Exceptions\Webhooks\WebhookDeletionFailed;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Webhooks\WebhookMock;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    config()->set('easypost.actions.delete_webhook', DeleteWebhookAction::class);

    Event::fake();

    mockWebhookApi([
        WebhookMock::make()
            ->withId('my_hook'),
        WebhookMock::make()
            ->usingMethod('delete')
            ->withId('my_hook'),
    ]);
});

afterEach(function () {
    app(WebhooksService::class)->resetMocks();
});

it('can delete a webhook', function () {
    app(DeleteWebhookActionContract::class)(webhookId: 'my_hook', testMode: false);

    Event::assertDispatched(function (WebhookWasDeleted $event) {
        return $event->webhookId === 'my_hook' && $event->testMode === false;
    });
});

it('does not dispatch events if the api request fails', function () {
    app(WebhooksService::class)->resetMocks();

    mockWebhookApi([
        WebhookMock::notFound(),
    ]);

    try {
        app(DeleteWebhookActionContract::class)(webhookId: 'my_hook', testMode: false);
    } catch (WebhookDeletionFailed) {
    }

    Event::assertNotDispatched(WebhookWasDeleted::class);
});
