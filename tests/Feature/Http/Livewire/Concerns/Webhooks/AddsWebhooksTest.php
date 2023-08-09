<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Actions\Webhooks\AddWebhookAction;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Webhooks\WebhookListMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Webhooks\WebhookMock;
use CybrixSolutions\EasyPost\Tests\Fixtures\Livewire\TestWebhooksComponent;
use Illuminate\Support\Facades\Event;
use function Pest\Livewire\livewire;

beforeEach(function () {
    config()->set('easypost.actions.add_webhook', AddWebhookAction::class);
    config()->set('app.url', 'https://example.com');
    config()->set('easypost.webhook_url', '/webhook');

    Event::fake();

    mockWebhookApi([
        WebhookMock::make()
            ->usingMethod('post')
            ->withUrl('https://example.com/webhook')
            ->withId('hook_test'),
    ]);
});

afterEach(function () {
    app(WebhooksService::class)->resetMocks();
});

it('shows a form to add a test webhook', function () {
    $component = new class extends TestWebhooksComponent
    {
        public function render(): string
        {
            return <<<'HTML'
            <div>
                @if ($this->showAddForm)
                    <form wire:submit.prevent="storeWebhook">
                        <input type="text" wire:model.defer="webhookState.url">
                    </form>
                @else
                    <button wire:click="addWebhook">Add test webhook</button>
                @endif
            </div>
            HTML;
        }
    };

    livewire($component::class)
        ->assertSeeText('Add test webhook')
        ->emit('webhook.add')
        ->assertSet('showAddForm', true)
        ->assertSee('<form', false)
        ->assertDontSeeText('Add test webhook')
        ->assertSet('webhookState.url', 'http://localhost/webhook');
});

it('adds a test webhook', function () {
    mockWebhookApi(testMocks: [
        WebhookMock::make()
            ->usingMethod('post')
            ->withUrl('https://example.com/webhook')
            ->withId('hook_test'),
    ]);

    $component = livewire(TestWebhooksComponent::class)
        ->assertDontSee('https://example.com/webhook')
        ->assertDontSee('ID: hook_test')
        ->emit('webhook.add')
        ->set('webhookState.url', 'https://example.com/webhook')
        ->call('storeWebhook')
        ->assertSet('showAddForm', false)
        ->assertSet('webhookState.url', '')
        ->assertEmitted('webhook.created', 'hook_test');

    app(WebhooksService::class)->resetMocks();

    mockWebhookApi(testMocks: [
        WebhookListMock::make()->testOnly(),
    ]);

    $component
        // We need to force the component to refresh...
        ->emit('webhook.created', 'hook_test')
        ->assertSee('https://example.com/webhook')
        ->assertSee('ID: hook_test');
});

it('handles api call errors when adding a test webhook', function () {
    mockWebhookApi(testMocks: [
        WebhookListMock::make(),
        WebhookMock::badRequest()
            ->usingMethod('post'),
    ]);

    $component = new class extends TestWebhooksComponent
    {
        public function render(): string
        {
            return <<<'HTML'
            <div>
                @if ($this->showAddForm)
                    <form wire:submit.prevent="storeWebhook">
                        <input type="text" wire:model.defer="webhookState.url">

                        @error('url')
                            <span>{{ $message }}</span>
                        @enderror
                    </form>
                @else
                    <button wire:click="addWebhook">Add test webhook</button>
                @endif
            </div>
            HTML;
        }
    };

    livewire($component::class)
        ->emit('webhook.add')
        ->assertDontSee('Malformed request')
        ->call('storeWebhook')
        ->assertHasErrors('url')
        ->assertSee('Malformed request')
        ->assertSet('showAddForm', true)
        ->assertNotEmitted('webhook.created');
});

it('requires a test webhook url', function () {
    mockWebhookApi(testMocks: [
        WebhookListMock::make(),
    ]);

    $component = new class extends TestWebhooksComponent
    {
        public function render(): string
        {
            return <<<'HTML'
            <div>
                @if ($this->showAddForm)
                    <form wire:submit.prevent="storeWebhook">
                        <input type="text" wire:model.defer="webhookState.url">

                        @error('url')
                            <span>{{ $message }}</span>
                        @enderror
                    </form>
                @else
                    <button wire:click="addWebhook">Add test webhook</button>
                @endif
            </div>
            HTML;
        }
    };

    livewire($component::class)
        ->emit('webhook.add')
        ->set('webhookState.url', '')
        ->assertDontSee('required')
        ->call('storeWebhook')
        ->assertHasErrors(['url' => 'required'])
        ->assertSee('required')
        ->assertSet('showAddForm', true)
        ->assertNotEmitted('webhook.created');
});
