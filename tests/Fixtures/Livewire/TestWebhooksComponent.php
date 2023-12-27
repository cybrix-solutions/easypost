<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Livewire;

use CybrixSolutions\EasyPost\Http\Livewire\Concerns\Webhooks\AddsWebhooks;
use CybrixSolutions\EasyPost\Http\Livewire\Concerns\Webhooks\DeletesWebhooks;
use CybrixSolutions\EasyPost\Http\Livewire\Concerns\Webhooks\ListsWebhooks;
use Livewire\Component;

class TestWebhooksComponent extends Component
{
    use AddsWebhooks;
    use DeletesWebhooks;
    use ListsWebhooks;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            @if ($this->deleteError)
                <p id="delete-error">{{ $this->deleteError }}</p>
            @endif

            <ul>
                @forelse ($this->webhooks as $webhook)
                    <li wire:key="hook{{ $webhook->id }}">
                        <x-easypost::webhook-result :webhook="$webhook" />
                    </li>
                @empty
                    <li id="no-results">No results</li>
                @endforelse
            </ul>
        </div>
        HTML;
    }

    protected function authorizeWebhookAdd(bool $testMode): void
    {
    }

    protected function authorizeWebhookDelete(): void
    {
    }
}
