<?php

declare(strict_types=1);

use CybrixSolutions\EasyPost\Livewire\WebhookManager;
use CybrixSolutions\EasyPost\Services\WebhooksService;
use CybrixSolutions\EasyPost\Tests\Fixtures\EasyPostMocks\Webhooks\WebhookListMock;

use function Pest\Livewire\livewire;

afterEach(function () {
    app(WebhooksService::class)->resetMocks();
});

it('renders webhooks in a filament table', function () {
    mockWebhookApi([
        WebhookListMock::make()->productionOnly(),
    ], [
        WebhookListMock::make()->testOnly(),
    ]);

    livewire(WebhookManager::class)
        ->assertTableColumnExists('url')
        ->assertTableColumnExists('mode')
        ->assertSee('https://example.com/webhook')
        ->assertSee(__('easypost::livewire/webhooks.modes.production'))
        ->assertSee(__('easypost::livewire/webhooks.modes.test'));
});
