<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\Webhooks\UpdateWebhookAction;
use CybrixSolutions\EasyPost\Commands\UpdateWebhookSecretsCommand;
use CybrixSolutions\EasyPost\Events\Webhooks\WebhookWasUpdated;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Webhooks\WebhookListMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Webhooks\WebhookMock;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\artisan;

beforeEach(function () {
    config()->set('easypost.actions.update_webhook', UpdateWebhookAction::class);

    Event::fake();

    mockWebhookApi([
        WebhookMock::make()
            ->withId('hook_prod'),
        WebhookListMock::make()->productionOnly(),
        WebhookMock::make()
            ->usingMethod('patch')
            ->withId('hook_prod'),
    ], [
        WebhookMock::make()
            ->withId('hook_test'),
        WebhookListMock::make()->testOnly(),
        WebhookMock::make()
            ->usingMethod('patch')
            ->withId('hook_test'),
    ]);
});

afterEach(function () {
    app(WebhooksService::class)->resetMocks();
});

it('updates webhook secrets for all registered webhooks', function () {
    artisan(UpdateWebhookSecretsCommand::class)
        ->assertSuccessful()
        ->expectsOutputToContain('hook_test')
        ->expectsOutputToContain('hook_prod');

    Event::assertDispatchedTimes(WebhookWasUpdated::class, 2);
});
