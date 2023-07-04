<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Livewire;

use CybrixSolutions\EasyPost\Http\Livewire\Concerns\SyncsCarrierAccounts;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class TestCarrierSyncComponent extends Component
{
    use AuthorizesRequests;
    use SyncsCarrierAccounts;

    public bool $success = false;

    protected function onSynced(): void
    {
        $this->success = true;
    }

    public function render(): string
    {
        return <<<'HTML'
        <div>
            @if ($this->show)
                <div id="sync-confirmation">Are you sure?</div>
            @else
                <div>Button to sync</div>

                @if ($this->success)
                    <div id="sync-success">Synced!</div>
                @endif
            @endif
        </div>
        HTML;
    }
}
