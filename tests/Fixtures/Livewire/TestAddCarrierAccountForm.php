<?php

declare(strict_types=1);

namespace CybrixSolutions\EasyPost\Tests\Fixtures\Livewire;

use CybrixSolutions\EasyPost\Http\Livewire\Concerns\AddsCarrierAccounts;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class TestAddCarrierAccountForm extends Component
{
    use AddsCarrierAccounts;
    use AuthorizesRequests;

    public bool $added = false;

    public function render(): string
    {
        return <<<'HTML'
        <div>
            @if ($this->show)
                <div id="add-carrier-form">
                    @if ($this->carrierService)
                        <div id="carrier-account-fields">
                            <x-easypost::carrier-account-form-fields
                                :carrier-service="$this->carrierService"
                            />
                        </div>
                    @else
                        <div id="carrier-search-form">
                            <ul>
                                @foreach ($this->filteredCarrierTypes as $carrierType)
                                    <li>{{ $carrierType->label() }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @else
                <div>Button to show the form</div>

                @if ($this->added)
                    <div id="added-alert">Account added!</div>
                @endif
            @endif
        </div>
        HTML;
    }

    protected function onAdded(): void
    {
        $this->added = true;
    }
}
