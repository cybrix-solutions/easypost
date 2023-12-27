<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\Webhooks\DeleteWebhookAction;
use CybrixSolutions\EasyPost\Events\Webhooks\WebhookWasDeleted;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Webhooks\WebhookMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\Livewire\TestWebhooksComponent;
use Illuminate\Support\Facades\Event;

use function Pest\Livewire\livewire;

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

it('confirms deletion of a webhook', function () {
    livewire(TestWebhooksComponent::class)
        ->emit('webhook.confirm_delete', 'my_hook', 'production')
        ->assertSet('showDelete', true)
        ->assertSet('deleting', 'my_hook')
        ->assertSet('deleteMode', 'production')
        ->assertSet('deleteError', null);
})->skip();

it('deletes a webhook', function () {
    livewire(TestWebhooksComponent::class)
        ->emit('webhook.confirm_delete', 'my_hook', 'production')
        ->call('deleteWebhook')
        ->assertSet('showDelete', false)
        ->assertSet('deleting', null)
        ->assertSet('deleteMode', null)
        ->assertSet('deleteError', null)
        ->assertEmitted('webhook.deleted', 'my_hook', 'production');

    Event::assertDispatched(function (WebhookWasDeleted $event) {
        return $event->webhookId === 'my_hook' && $event->testMode === false;
    });
})->skip();

it('can execute extra code when a webhook is deleted', function () {
    $component = new class extends TestWebhooksComponent
    {
        public bool $deleted = false;

        protected function onWebhookDelete(): void
        {
            $this->deleted = true;
        }
    };

    livewire($component::class)
        ->assertSet('deleted', false)
        ->emit('webhook.confirm_delete', 'my_hook', 'production')
        ->call('deleteWebhook')
        ->assertSet('deleted', true);
})->skip();

it('sets an error message if the api call fails', function () {
    app(WebhooksService::class)->resetMocks();

    mockWebhookApi([
        WebhookMock::notFound(),
    ]);

    livewire(TestWebhooksComponent::class)
        ->assertDontSee('delete-error')
        ->emit('webhook.confirm_delete', 'my_hook', 'production')
        ->call('deleteWebhook')
        ->assertNotEmitted('webhook.deleted')
        ->assertSet('showDelete', true)
        ->assertSeeText('The requested resource could not be found')
        ->assertSee('delete-error');
})->skip();

test('custom authorization can be performed when deleting a webhook', function () {
    $class = new class extends TestWebhooksComponent
    {
        protected function authorizeWebhookDelete(): void
        {
            abort(403);
        }
    };

    livewire($class::class)
        ->emit('webhook.confirm_delete', 'my_hook', 'production')
        ->call('deleteWebhook')
        ->assertForbidden();
})->skip();
